<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use Tenantable;

    protected $table = 'subjects';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'subj_';
    protected $guarded = ['id', 'school_id'];

}
