<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    use Tenantable;

    protected $table = 'student_fees';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'sf_';
    protected $guarded = ['id', 'school_id'];

}
