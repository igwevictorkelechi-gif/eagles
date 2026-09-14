<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    use Tenantable;

    protected $table = 'subscription_payments';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'spay_';
    protected $guarded = ['id', 'school_id'];
}
