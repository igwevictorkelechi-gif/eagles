<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use Tenantable;

    protected $table = 'sale_items';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'si_';
    protected $guarded = ['id', 'school_id'];
    public $timestamps = false;
}
