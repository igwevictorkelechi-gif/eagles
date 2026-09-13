<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    use Tenantable;

    protected $table = 'exam_questions';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'eq_';
    protected $guarded = ['id', 'school_id'];
    public $timestamps = false;
}
