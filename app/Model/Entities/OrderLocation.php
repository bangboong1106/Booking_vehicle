<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class OrderLocation extends ModelSoftDelete
{
    protected $table = "order_locations";

    protected $_alias = 'order_location';
    protected $fillable = ['order_id', 'location_id', 'type', 'date', 'date_reality', 'time','time_reality','note'];


}