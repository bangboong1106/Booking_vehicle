<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

/**
 * @property integer id
 */
class LocationGroup extends ModelSoftDelete
{
    protected $table = "location_group";

    protected $_alias = 'location_group';
    protected $fillable = ['title', 'code', 'description', 'customer_id'];

    public $location_ids;

    public function locations()
    {
        return $this->hasMany(Location::class, 'location_group_id');
    }

    public function selectedLocations()
    {
        return Location::query()->select('*')->whereIn('id', $this->location_ids)->get();
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
}
