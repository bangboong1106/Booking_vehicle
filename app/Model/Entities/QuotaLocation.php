<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class QuotaLocation extends ModelSoftDelete
{
    protected $table = "quota_location";

    protected $_alias = 'quota_location';
    protected $fillable = ['quota_id', 'location_id', 'location_title', 'location_order'];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id')->select(['id', 'location_group_id']);
    }
}