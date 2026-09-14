<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\School;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private array $ctx = [];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['alpha', 'beta'] as $slug) {
            $school = School::create(['name' => ucfirst($slug), 'slug' => $slug]);
            $admin = User::create([
                'school_id' => $school->id, 'role' => 'school_admin',
                'first_name' => ucfirst($slug), 'last_name' => 'Admin',
                'email' => "admin@$slug.test", 'password' => 'Password123!', 'email_verified' => true,
            ]);
            // A student that belongs only to this school. school_id is guarded
            // against mass assignment, so set it explicitly for the fixture.
            $student = new Student([
                'admission_no' => strtoupper($slug) . '/001',
                'first_name' => ucfirst($slug), 'last_name' => 'Pupil', 'status' => 'active',
            ]);
            $student->school_id = $school->id;
            $student->save();
            $this->ctx[$slug] = ['school' => $school, 'admin' => $admin];
        }
    }

    public function test_a_school_only_sees_its_own_students(): void
    {
        $this->actingAs($this->ctx['alpha']['admin']);

        $this->get('/app/students')
            ->assertOk()
            ->assertSee('ALPHA/001')
            ->assertDontSee('BETA/001');
    }

    public function test_the_global_scope_hides_other_schools_rows_from_queries(): void
    {
        $this->actingAs($this->ctx['alpha']['admin']);

        $this->assertSame(1, Student::count());
        $this->assertSame('ALPHA/001', Student::first()->admission_no);
    }

    public function test_a_school_cannot_edit_another_schools_record(): void
    {
        $betaStudent = Student::withoutGlobalScopes()
            ->where('school_id', $this->ctx['beta']['school']->id)->firstOrFail();

        $this->actingAs($this->ctx['alpha']['admin']);

        // Even with a valid id from another tenant, the scoped lookup must 404.
        $this->put("/app/students/{$betaStudent->id}", [
            'first_name' => 'Hacked', 'last_name' => 'Name',
        ])->assertNotFound();

        $this->assertSame(ucfirst('beta'), $betaStudent->fresh()->first_name);
    }

    public function test_created_rows_are_tagged_with_the_current_school(): void
    {
        $this->actingAs($this->ctx['alpha']['admin']);

        $this->post('/app/students', ['first_name' => 'New', 'last_name' => 'Pupil'])
            ->assertRedirect('/app/students');

        $created = Student::where('first_name', 'New')->firstOrFail();
        $this->assertSame($this->ctx['alpha']['school']->id, $created->school_id);
    }

    public function test_pos_checkout_decrements_stock_and_records_income(): void
    {
        $this->actingAs($this->ctx['alpha']['admin']);

        $product = Product::create([
            'name' => 'Exercise Book', 'selling_price' => 250, 'stock_qty' => 10, 'low_stock' => 2,
        ]);

        $this->post('/app/pos', ['qty' => [$product->id => 3], 'payment_method' => 'cash'])
            ->assertRedirect('/app/pos');

        $this->assertSame(7, $product->fresh()->stock_qty);
        $this->assertSame(750, (int) Transaction::where('type', 'income')->sum('amount'));
    }
}
