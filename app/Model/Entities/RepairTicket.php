<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Exception;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

class RepairTicket extends ModelSoftDelete implements Auditable
{
    protected $table = "repair_ticket";

    protected $fillable = [
        'code',
        'name',
        'driver_id',
        'vehicle_id',
        'repair_date',
        'description',
        'amount',
        'is_approved',
        'approved_id',
        'approved_date',
        'approved_note',
    ];

    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'code',
        'name',
        'driver_id',
        'vehicle_id',
        'repair_date',
        'description',
        'amount',
        'is_approved',
        'approved_id',
        'approved_date',
        'approved_note',
    ];

    public function driver()
    {
        return $this->hasOne(Driver::class, 'id', 'driver_id');
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'id', 'vehicle_id');
    }

    public function approvedUser()
    {
        return $this->hasOne(AdminUserInfo::class, 'id', 'approved_id');
    }

    public function items()
    {
        return $this->hasMany(RepairTicketItem::class, 'repair_ticket_id', 'id');
    }


    public function repairTicketItems()
    {
        return $this->belongsToMany(Accessory::class, 'repair_ticket_item', 'repair_ticket_id', 'accessory_id')
            ->withPivot(
                'quantity',
                'price',
                'amount',
                'next_repair_date',
                'next_repair_distance'
            )
            ->wherePivot('del_flag', '=', 0);
    }
}
