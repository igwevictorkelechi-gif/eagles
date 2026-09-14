<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use Tenantable;

    protected $table = 'classes';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'cls_';
    protected $guarded = ['id', 'school_id'];

}
