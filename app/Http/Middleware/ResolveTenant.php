<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstallController;
use App\Models\School;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the current school from a request served on a per-school subdomain
 * (e.g. greenfield.sas.app). Only active when APP_DOMAIN is configured and the
 * app is installed; otherwise the app runs in single-domain mode untouched.
 */
class ResolveTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $domain = config('app.domain');

        // Single-domain mode, or the installer hasn't run yet: do nothing.
        if (! $domain || ! InstallController::isInstalled()) {
            return $next($request);
        }

        $host = strtolower($request->getHost());
        $domain = strtolower($domain);

        // Requests on the bare apex or "www" are the marketing/platform site.
        if ($host === $domain || $host === 'www.' . $domain) {
            return $next($request);
        }

        // Only handle hosts that are direct subdomains of APP_DOMAIN.
        if (! str_ends_with($host, '.' . $domain)) {
            return $next($request);
        }

        $slug = substr($host, 0, -1 * (strlen($domain) + 1));

        // Nested labels (a.b.sas.app) aren't valid school subdomains.
        if ($slug === '' || str_contains($slug, '.')) {
            return $next($request);
        }

        $school = School::where('slug', $slug)->first();
        abort_if(! $school, 404, 'Unknown school.');

        // Make the tenant available application-wide for this request.
        app()->instance('tenant.school', $school);
        View::share('tenant', $school);

        // The subdomain root sends people straight where they belong.
        if ($request->path() === '/' || $request->path() === '') {
            $user = $request->user();
            return $user
                ? redirect(AuthController::homeFor($user))
                : redirect()->route('login');
        }

        return $next($request);
    }
}
