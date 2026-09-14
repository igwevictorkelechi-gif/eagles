<?php

namespace App\Models;

use App\Models\Concerns\Tenantable;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use Tenantable;

    protected $table = 'announcements';
    protected $keyType = 'string';
    public $incrementing = false;
    public $idPrefix = 'ann_';
    protected $guarded = ['id', 'school_id'];

}
