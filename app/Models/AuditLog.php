<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use Tenantable;

    protected $table = 'audit_logs';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'aud_';
    protected $guarded = ['id', 'school_id'];

}
