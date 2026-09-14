<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use Tenantable;

    protected $table = 'sales';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'sale_';
    protected $guarded = ['id', 'school_id'];

}
