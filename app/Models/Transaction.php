<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use Tenantable;

    protected $table = 'transactions';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'txn_';
    protected $guarded = ['id', 'school_id'];

}
