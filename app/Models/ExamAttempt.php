<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use Tenantable;

    protected $table = 'exam_attempts';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'ea_';
    protected $guarded = ['id', 'school_id'];
    public $timestamps = false;
}
