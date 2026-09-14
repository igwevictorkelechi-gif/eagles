<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use Tenantable;

    protected $table = 'students';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'std_';
    protected $guarded = ['id', 'school_id'];

}
