<?php

namespace App\Http\Middleware;

use App\Http\Controllers\InstallController;
use Closure;
use Illuminate\Http\Request;

// Until the app is installed, every page redirects to the setup wizard.
// Once storage/installed exists, the installer disappears.
class EnsureInstalled
{
    public function handle(Request $request, Closure $next)
    {
        $installed = InstallController::isInstalled();

        if ($request->is('install', 'install/*')) {
            // Don't let anyone re-run the wizard once installed.
            if ($installed) {
                return redirect('/');
            }
            return $next($request);
        }

        if (! $installed) {
            return redirect()->route('install.requirements');
        }

        return $next($request);
    }
}
