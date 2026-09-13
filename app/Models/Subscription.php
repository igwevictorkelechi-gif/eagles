<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscription extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = ['id'];

    protected $casts = [
        'trial_ends_at' => 'datetime',
        'current_period_end' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(fn ($m) => $m->id = $m->id ?: 'sub_' . Str::uuid());
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
