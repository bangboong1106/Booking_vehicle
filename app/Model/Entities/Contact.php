<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class Contact extends ModelSoftDelete
{
    protected $table = "contact";
    protected $_alias = 'contact';

    protected $fillable = [
        'contact_name',
        'email',
        'phone_number',
        'location_id',
        'customer_id',
        'full_address',
        'location_title',
        'active'
    ];
    protected $_detailNameField = 'contact_name';

    protected static $_destroyRelations = [];

    public function getActive()
    {
        return config('system.active.' . $this->active);
    }

    public function locationRel()
    {
       return  $this->hasOne(Location::class, 'id', 'location_id');
    }


}
