<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use Tenantable;

    protected $table = 'student_attendance';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'att_';
    protected $guarded = ['id', 'school_id'];

}
