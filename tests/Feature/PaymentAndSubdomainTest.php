<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentAndSubdomainTest extends TestCase
{
    use RefreshDatabase;

    private School $school;
    private User $admin;
    private SubscriptionPlan $plan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->plan = SubscriptionPlan::create([
            'id' => 'plan_starter', 'name' => 'Starter', 'code' => 'starter',
            'price_monthly' => 15000, 'currency' => 'NGN', 'is_custom' => false, 'is_active' => true, 'sort_order' => 1,
        ]);

        $this->school = School::create(['name' => 'Greenfield', 'slug' => 'greenfield']);
        $this->admin = User::create([
            'school_id' => $this->school->id, 'role' => 'school_admin',
            'first_name' => 'Green', 'last_name' => 'Admin',
            'email' => 'admin@greenfield.test', 'password' => 'Password123!', 'email_verified' => true,
        ]);
    }

    public function test_checkout_creates_a_pending_payment_and_redirects_to_the_gateway(): void
    {
        config(['services.paystack.secret' => 'sk_test_x', 'services.paystack.public' => 'pk_test_x']);

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/abc123', 'reference' => 'ref-echo'],
            ], 200),
        ]);

        $this->actingAs($this->admin)
            ->post('/app/billing/checkout', ['plan_id' => $this->plan->id, 'gateway' => 'paystack'])
            ->assertRedirect('https://checkout.paystack.com/abc123');

        $payment = SubscriptionPayment::withoutGlobalScopes()->firstOrFail();
        $this->assertSame('pending', $payment->status);
        $this->assertSame('paystack', $payment->gateway);
        $this->assertSame(15000, (int) $payment->amount);
        $this->assertSame($this->school->id, $payment->school_id);
    }

    public function test_a_verified_callback_activates_the_subscription(): void
    {
        config(['services.paystack.secret' => 'sk_test_x']);

        $payment = new SubscriptionPayment([
            'gateway' => 'paystack', 'reference' => 'PAYSTACK_REF1', 'amount' => 15000,
            'currency' => 'NGN', 'status' => 'pending', 'email' => $this->admin->email,
            'plan_id' => $this->plan->id,
        ]);
        $payment->school_id = $this->school->id;
        $payment->save();

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => ['status' => 'success', 'amount' => 1500000],
            ], 200),
        ]);

        $this->get('/pay/callback/paystack?reference=PAYSTACK_REF1')
            ->assertRedirect(route('app.billing'));

        $this->assertSame('success', $payment->fresh()->status);

        $sub = Subscription::where('school_id', $this->school->id)->latest()->first();
        $this->assertNotNull($sub);
        $this->assertSame('active', $sub->status);
    }

    public function test_settlement_is_idempotent(): void
    {
        config(['services.paystack.secret' => 'sk_test_x']);

        $payment = new SubscriptionPayment([
            'gateway' => 'paystack', 'reference' => 'PAYSTACK_REF2', 'amount' => 15000,
            'currency' => 'NGN', 'status' => 'success', 'email' => $this->admin->email,
            'plan_id' => $this->plan->id,
        ]);
        $payment->school_id = $this->school->id;
        $payment->save();

        // Already-successful payments must not hit the gateway again.
        Http::fake();

        $this->get('/pay/callback/paystack?reference=PAYSTACK_REF2')
            ->assertRedirect(route('app.billing'));

        Http::assertNothingSent();
    }

    public function test_webhook_rejects_an_invalid_signature(): void
    {
        config(['services.paystack.secret' => 'sk_test_secret']);

        $payload = json_encode(['event' => 'charge.success', 'data' => ['reference' => 'PAYSTACK_REF3']]);

        $this->call('POST', '/pay/webhook/paystack', [], [], [],
            ['HTTP_X_PAYSTACK_SIGNATURE' => 'wrong', 'CONTENT_TYPE' => 'application/json'], $payload)
            ->assertStatus(400);
    }

    public function test_webhook_settles_with_a_valid_signature(): void
    {
        config(['services.paystack.secret' => 'sk_test_secret']);

        $payment = new SubscriptionPayment([
            'gateway' => 'paystack', 'reference' => 'PAYSTACK_REF4', 'amount' => 15000,
            'currency' => 'NGN', 'status' => 'pending', 'email' => $this->admin->email,
            'plan_id' => $this->plan->id,
        ]);
        $payment->school_id = $this->school->id;
        $payment->save();

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true, 'data' => ['status' => 'success', 'amount' => 1500000],
            ], 200),
        ]);

        $payload = json_encode(['event' => 'charge.success', 'data' => ['reference' => 'PAYSTACK_REF4']]);
        $signature = hash_hmac('sha512', $payload, 'sk_test_secret');

        $this->call('POST', '/pay/webhook/paystack', [], [], [],
            ['HTTP_X_PAYSTACK_SIGNATURE' => $signature, 'CONTENT_TYPE' => 'application/json'], $payload)
            ->assertOk();

        $this->assertSame('success', $payment->fresh()->status);
    }

    public function test_a_school_subdomain_resolves_the_tenant_and_scopes_login(): void
    {
        config(['app.domain' => 'sas.test']);

        // The subdomain root redirects a guest to login.
        $this->get('http://greenfield.sas.test/')
            ->assertRedirect(route('login'));

        // Login on the subdomain authenticates against that school.
        $this->post('http://greenfield.sas.test/login', [
            'email' => $this->admin->email, 'password' => 'Password123!',
        ])->assertRedirect(route('app.dashboard'));

        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_an_unknown_subdomain_is_a_404(): void
    {
        config(['app.domain' => 'sas.test']);

        $this->get('http://nosuchschool.sas.test/login')->assertNotFound();
    }

    public function test_the_apex_domain_is_not_treated_as_a_tenant(): void
    {
        config(['app.domain' => 'sas.test']);

        // The marketing home on the bare domain stays public, no tenant bound.
        $this->get('http://sas.test/')->assertOk();
        $this->assertFalse(app()->bound('tenant.school'));
    }
}
