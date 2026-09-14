<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id', 'school_id', 'role', 'first_name', 'last_name', 'email',
        'phone', 'password', 'photo_url', 'is_active', 'email_verified', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'email_verified' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $u) {
            if (empty($u->id)) $u->id = 'usr_' . Str::uuid();
        });
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function getNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }
}
