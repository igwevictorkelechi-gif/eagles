<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class School extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::creating(fn ($m) => $m->id = $m->id ?: 'sch_' . Str::uuid());
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class, 'school_id')->latestOfMany();
    }
}
