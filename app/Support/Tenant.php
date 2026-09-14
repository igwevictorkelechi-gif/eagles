<?php

namespace App\Support;

use App\Models\School;

class Tenant
{
    /** The school resolved from the current request's subdomain, or null. */
    public static function current(): ?School
    {
        return app()->bound('tenant.school') ? app('tenant.school') : null;
    }

    /** Build a school's full subdomain URL, e.g. https://greenfield.sas.app */
    public static function url(School $school): ?string
    {
        $domain = config('app.domain');
        if (! $domain) {
            return null;
        }
        $scheme = str_starts_with((string) config('app.url'), 'https') ? 'https' : 'http';
        return "{$scheme}://{$school->slug}.{$domain}";
    }
}
