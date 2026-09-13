<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use Tenantable;

    protected $table = 'exams';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'exam_';
    protected $guarded = ['id', 'school_id'];

}
