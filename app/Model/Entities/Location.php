<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class Location extends ModelSoftDelete
{
    protected $table = "locations";

    protected $_alias = 'location';
    protected $fillable = ['title', 'address', 'location_type_id', 'ward_id', 'district_id', 'longitude', 'latitude', 'province_id',
        'customer_id', 'address_auto_code', 'full_address', 'code','ins_id','upd_id','upd_date','del_flag', 'limited_day',
        'location_group_id'];
    protected $_detailNameField = 'code';

    protected $nullable = [
        'location_group_id'
    ];

    public function district()
    {
        return $this->hasOne(District::class, 'district_id', 'district_id');
    }

    public function ward()
    {
        return $this->hasOne(Ward::class, 'ward_id', 'ward_id');
    }

    public function province()
    {
        return $this->hasOne(Province::class, 'province_id', 'province_id');
    }

    public function type()
    {
        return $this->hasOne(LocationType::class, 'id', 'location_type_id');
    }

    public function group()
    {
        return $this->hasOne(LocationGroup::class, 'id', 'location_group_id');
    }

    public function getCurrentLocation()
    {
        $provinceTitle = $this->tryGet('province')->title;
        $districtTitle = $this->tryGet('district')->title;
        $wardTitle = $this->tryGet('ward')->title;

        if (empty($this->id)) {
            return '';
        }

        return $this->address . ', ' . $wardTitle . ', ' . $districtTitle . ', ' . $provinceTitle;
    }

    public function getFullLocation()
    {
        return $this->title . ' ('.$this->full_address.')';
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }
}