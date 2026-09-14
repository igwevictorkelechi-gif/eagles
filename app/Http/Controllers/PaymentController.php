<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Services\Payments\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Handles the gateway return (browser callback) and the server-to-server webhook.
class PaymentController extends Controller
{
    // Browser is redirected back here after paying.
    public function callback(Request $request, string $gateway)
    {
        $reference = $request->query('reference') ?? $request->query('trxref') ?? $request->query('ref');
        if (! $reference) {
            return redirect()->route('app.billing')->with('err', 'No payment reference returned.');
        }

        $applied = $this->settle($gateway, $reference);

        return redirect()->route('app.billing')->with(
            $applied ? 'ok' : 'err',
            $applied ? 'Payment successful — your subscription is active. 🎉' : 'Payment could not be verified.'
        );
    }

    // Server-to-server webhook (no auth, no CSRF). Verified by signature.
    public function webhook(Request $request, string $gateway)
    {
        try {
            $g = PaymentManager::get($gateway);
        } catch (\Throwable) {
            return response('unknown gateway', 404);
        }

        $reference = $g->parseWebhook($request);
        if (! $reference) {
            return response('invalid', 400);
        }

        $this->settle($gateway, $reference);
        return response('ok', 200);
    }

    // Verify with the gateway and, if paid, activate the subscription (idempotent).
    private function settle(string $gateway, string $reference): bool
    {
        $payment = SubscriptionPayment::withoutGlobalScopes()->where('reference', $reference)->first();
        if (! $payment) {
            return false;
        }
        if ($payment->status === 'success') {
            return true; // already applied
        }

        try {
            $result = PaymentManager::get($gateway)->verify($reference);
        } catch (\Throwable $e) {
            Log::warning('Payment verify failed', ['ref' => $reference, 'err' => $e->getMessage()]);
            return false;
        }

        if (! ($result['status'] ?? false)) {
            $payment->update(['status' => 'failed', 'meta' => json_encode($result['raw'] ?? [])]);
            return false;
        }

        $payment->update(['status' => 'success', 'meta' => json_encode($result['raw'] ?? [])]);

        // Activate / extend the school's subscription.
        $plan = SubscriptionPlan::find($payment->plan_id);
        $sub = Subscription::where('school_id', $payment->school_id)->latest()->first();
        $periodEnd = now()->addMonth();

        if ($sub) {
            $sub->update([
                'plan_id' => $payment->plan_id ?: $sub->plan_id,
                'status' => 'active',
                'current_period_end' => $periodEnd,
            ]);
        } else {
            Subscription::create([
                'school_id' => $payment->school_id,
                'plan_id' => $payment->plan_id,
                'status' => 'active',
                'current_period_end' => $periodEnd,
            ]);
        }

        return true;
    }
}
