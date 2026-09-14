<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    use Tenantable;

    protected $table = 'fee_payments';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'pay_';
    protected $guarded = ['id', 'school_id'];

}
