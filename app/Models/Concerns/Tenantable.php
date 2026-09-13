<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

// Multi-tenant isolation: every query by a school-scoped user is constrained to
// their school_id, and new rows get a uuid + the current school_id automatically.
// Super admins (school_id = null) are not constrained here — they use the
// platform models (School, SubscriptionPlan, Subscription), not tenant models.
trait Tenantable
{
    // Note: string/non-incrementing keys are declared on each model class
    // (a trait cannot redeclare Model::$keyType).

    public static function bootTenantable(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            $user = Auth::user();
            if ($user && $user->school_id) {
                $builder->where($builder->getModel()->getTable() . '.school_id', $user->school_id);
            }
        });

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = ($model->idPrefix ?? '') . Str::uuid();
            }
            if (empty($model->school_id) && Auth::check() && Auth::user()->school_id) {
                $model->school_id = Auth::user()->school_id;
            }
        });
    }
}
