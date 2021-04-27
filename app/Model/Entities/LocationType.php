<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class LocationType extends ModelSoftDelete
{
    protected $table = "location_type";

    protected $_alias = 'location_type';
    protected $fillable = ['title', 'code', 'description', 'customer_id'];

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
}