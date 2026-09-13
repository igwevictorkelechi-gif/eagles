<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use Tenantable;

    protected $table = 'terms';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'trm_';
    protected $guarded = ['id', 'school_id'];

}
