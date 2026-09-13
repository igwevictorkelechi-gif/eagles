<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use Tenantable;

    protected $table = 'assignments';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'asg_';
    protected $guarded = ['id', 'school_id'];

}
