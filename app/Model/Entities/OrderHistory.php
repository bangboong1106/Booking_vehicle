<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;

class OrderHistory extends ModelSoftDelete
{
    protected $table = "order_history";
    protected $fillable = ['order_id', 'vehicle_id', 'primary_driver_id', 'secondary_driver_id', 'customer_id', 'order_status',
        'latitude', 'longitude', 'current_location'];

    protected static $_destroyRelations = [
        'order', 'vehicle', 'driver'
    ];

    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'id', 'vehicle_id');
    }

    public function driver()
    {
        return $this->hasOne(Driver::class, 'id', 'driver_id');
    }

    public function getOrderStatus()
    {
        return config('system.order_status.' . $this->order_status);
    }
}