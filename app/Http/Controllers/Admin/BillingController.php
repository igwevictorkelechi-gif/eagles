<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\SubscriptionPlan;
use App\Services\Payments\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $subscription = Subscription::where('school_id', $schoolId)->latest()->first();
        $currentPlan = $subscription ? SubscriptionPlan::find($subscription->plan_id) : null;

        return view('app.billing', [
            'subscription' => $subscription,
            'currentPlan' => $currentPlan,
            'plans' => SubscriptionPlan::where('is_active', true)->orderBy('sort_order')->get(),
            'gateways' => PaymentManager::enabled(),
            'payments' => SubscriptionPayment::where('school_id', $schoolId)->orderByDesc('created_at')->limit(10)->get(),
        ]);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'exists:subscription_plans,id'],
            'gateway' => ['required', 'in:paystack,cheqpay'],
        ]);

        $plan = SubscriptionPlan::findOrFail($data['plan_id']);
        if ($plan->is_custom) {
            return back()->with('err', 'Enterprise is custom-priced — please contact sales.');
        }
        if ((int) $plan->price_monthly <= 0) {
            return back()->with('err', 'This plan is free; no payment needed.');
        }

        $gateway = PaymentManager::get($data['gateway']);
        if (! $gateway->configured()) {
            return back()->with('err', $gateway->label() . ' is not configured yet. Add its API keys in .env.');
        }

        $user = Auth::user();
        $reference = strtoupper($gateway->key()) . '_' . Str::upper(Str::random(10));

        $payment = SubscriptionPayment::create([
            'plan_id' => $plan->id,
            'subscription_id' => optional(Subscription::where('school_id', $user->school_id)->latest()->first())->id,
            'gateway' => $gateway->key(),
            'reference' => $reference,
            'amount' => (int) $plan->price_monthly,
            'currency' => $plan->currency ?: 'NGN',
            'status' => 'pending',
            'email' => $user->email,
            'meta' => json_encode(['plan' => $plan->name]),
        ]);

        try {
            $init = $gateway->initialize(
                (int) $plan->price_monthly,
                $user->email,
                $reference,
                route('pay.callback', ['gateway' => $gateway->key()]),
                ['school_id' => $user->school_id, 'plan_id' => $plan->id, 'payment_id' => $payment->id],
            );
        } catch (\Throwable $e) {
            $payment->update(['status' => 'failed', 'meta' => json_encode(['error' => $e->getMessage()])]);
            return back()->with('err', 'Could not start payment: ' . $e->getMessage());
        }

        return redirect()->away($init['authorization_url']);
    }
}
