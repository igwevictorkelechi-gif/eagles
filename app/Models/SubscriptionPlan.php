<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = ['id'];

    protected $casts = [
        'is_custom' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(fn ($m) => $m->id = $m->id ?: 'plan_' . Str::uuid());
    }

    public function featureList(): array
    {
        try { return json_decode($this->features ?: '[]', true) ?: []; }
        catch (\Throwable) { return []; }
    }
}
