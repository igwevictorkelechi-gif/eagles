<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use Tenantable;

    protected $table = 'results';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'res_';
    protected $guarded = ['id', 'school_id'];

}
