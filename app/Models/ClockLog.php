<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class ClockLog extends Model
{
    use Tenantable;

    protected $table = 'clock_logs';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'clk_';
    protected $guarded = ['id', 'school_id'];

}
