<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }
        if (! in_array($user->role, $roles, true)) {
            abort(403, 'You do not have access to this area.');
        }
        return $next($request);
    }
}
