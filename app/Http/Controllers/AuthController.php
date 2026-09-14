<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // When served from a school's subdomain, scope the login to that
        // school so users only ever authenticate against their own tenant.
        $tenant = Tenant::current();

        $query = User::where('email', $data['email'])->where('is_active', true);
        if ($tenant) {
            $query->where('school_id', $tenant->id);
        } else {
            // Prefer a super admin (school_id null), else any active user.
            $query->orderByRaw('CASE WHEN school_id IS NULL THEN 0 ELSE 1 END');
        }
        $user = $query->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }

        Auth::login($user, true);
        $user->forceFill(['last_login_at' => now()])->saveQuietly();
        $request->session()->regenerate();

        return redirect()->intended(self::homeFor($user));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:120'],
            'admin_first_name' => ['required', 'string', 'max:80'],
            'admin_last_name' => ['nullable', 'string', 'max:80'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
        ]);

        $slug = Str::slug($data['school_name']) ?: 'school';
        if (School::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower(Str::random(4));
        }

        $school = School::create([
            'name' => $data['school_name'],
            'slug' => $slug,
            'email' => $data['email'],
        ]);

        $plan = SubscriptionPlan::where('code', 'free')->first()
            ?? SubscriptionPlan::orderBy('sort_order')->first();
        if ($plan) {
            Subscription::create([
                'school_id' => $school->id,
                'plan_id' => $plan->id,
                'status' => 'trial',
                'trial_ends_at' => now()->addDays(14),
                'current_period_end' => now()->addDays(14),
            ]);
        }

        $user = User::create([
            'school_id' => $school->id,
            'role' => 'school_admin',
            'first_name' => $data['admin_first_name'],
            'last_name' => $data['admin_last_name'] ?? '',
            'email' => $data['email'],
            'password' => $data['password'],
            'email_verified' => true,
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route('app.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public static function homeFor(User $user): string
    {
        return match ($user->role) {
            'super_admin' => route('platform.dashboard'),
            'student', 'parent' => route('student.dashboard'),
            'sales_staff' => route('app.pos'),
            default => route('app.dashboard'),
        };
    }
}
