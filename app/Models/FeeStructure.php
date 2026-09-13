<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use Tenantable;

    protected $table = 'fee_structures';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'fee_';
    protected $guarded = ['id', 'school_id'];

}
