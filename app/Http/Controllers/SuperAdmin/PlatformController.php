<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlatformController extends Controller
{
    public function dashboard()
    {
        $mrr = (int) Subscription::where('status', 'active')
            ->join('subscription_plans', 'subscription_plans.id', '=', 'subscriptions.plan_id')
            ->sum('subscription_plans.price_monthly');

        return view('platform.dashboard', [
            'counts' => [
                'schools' => School::count(),
                'active' => Subscription::where('status', 'active')->count(),
                'trials' => Subscription::where('status', 'trial')->count(),
                'users' => User::count(),
                'plans' => SubscriptionPlan::where('is_active', true)->count(),
            ],
            'mrr' => $mrr,
        ]);
    }

    public function schools()
    {
        $schools = School::orderByDesc('created_at')->get()->map(function ($s) {
            $sub = Subscription::where('school_id', $s->id)->latest()->first();
            $s->sub_status = $sub?->status;
            $s->plan_name = $sub ? optional(SubscriptionPlan::find($sub->plan_id))->name : null;
            $s->students_count = \App\Models\Student::withoutGlobalScopes()->where('school_id', $s->id)->count();
            return $s;
        });
        return view('platform.schools', ['schools' => $schools]);
    }

    public function toggleSchool(Request $request, string $id)
    {
        $s = School::findOrFail($id);
        $s->update(['status' => $s->status === 'suspended' ? 'active' : 'suspended']);
        return back()->with('ok', 'School status updated.');
    }

    public function plans()
    {
        return view('platform.plans', ['plans' => SubscriptionPlan::orderBy('sort_order')->get()]);
    }

    public function storePlan(Request $request)
    {
        $data = $this->planData($request, true);
        SubscriptionPlan::create($data);
        return redirect()->route('platform.plans')->with('ok', 'Plan created.');
    }

    public function updatePlan(Request $request, string $id)
    {
        $data = $this->planData($request, false);
        SubscriptionPlan::findOrFail($id)->update($data);
        return redirect()->route('platform.plans')->with('ok', 'Plan updated.');
    }

    public function destroyPlan(string $id)
    {
        SubscriptionPlan::findOrFail($id)->update(['is_active' => false]);
        return redirect()->route('platform.plans')->with('ok', 'Plan deactivated.');
    }

    private function planData(Request $request, bool $withCode): array
    {
        $rules = [
            'name' => ['required'],
            'price_monthly' => ['nullable', 'numeric'],
            'currency' => ['nullable'],
            'max_students' => ['nullable', 'numeric'],
            'max_teachers' => ['nullable', 'numeric'],
            'sort_order' => ['nullable', 'numeric'],
        ];
        if ($withCode) $rules['code'] = ['required', 'alpha_dash'];
        $data = $request->validate($rules);
        $data['is_custom'] = $request->boolean('is_custom');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['currency'] = $data['currency'] ?? 'NGN';
        $data['features'] = json_encode(array_values(array_filter(
            array_map('trim', explode(',', (string) $request->input('features', ''))))));
        return $data;
    }

    public function subscriptions()
    {
        $subs = Subscription::orderByDesc('created_at')->get()->map(function ($s) {
            $s->school_name = optional(School::find($s->school_id))->name;
            $plan = SubscriptionPlan::find($s->plan_id);
            $s->plan_name = $plan?->name;
            $s->price_monthly = $plan?->price_monthly ?? 0;
            return $s;
        });
        return view('platform.subscriptions', ['subs' => $subs]);
    }

    public function updateSubscription(Request $request, string $id)
    {
        $data = $request->validate(['status' => ['required', 'in:trial,active,expired,suspended,cancelled']]);
        Subscription::findOrFail($id)->update($data);
        return back()->with('ok', 'Subscription updated.');
    }
}
