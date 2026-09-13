<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class SessionAcademic extends Model
{
    use Tenantable;

    protected $table = 'sessions_academic';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'ses_';
    protected $guarded = ['id', 'school_id'];

}
