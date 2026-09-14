<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Tenantable;

    protected $table = 'products';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'prod_';
    protected $guarded = ['id', 'school_id'];

}
