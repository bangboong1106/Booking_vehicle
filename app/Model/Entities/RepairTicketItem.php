<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Exception;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

class RepairTicketItem extends ModelSoftDelete implements Auditable
{
    protected $table = "repair_ticket_item";

    protected $fillable = [
        'repair_ticket_id',
        'accessory_id',
        'quantity',
        'price',
        'amount',
        'next_repair_type',
        'next_repair_date',
        'next_repair_distance',
    ];

    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'repair_ticket_id',
        'accessory_id',
        'quantity',
        'price',
        'amount',
        'next_repair_type',
        'next_repair_date',
        'next_repair_distance',
    ];

    public function accessory()
    {
        return $this->hasOne(Accessory::class, 'id', 'accessory_id');
    }

    public function repairTicket()
    {
        return $this->hasOne(RepairTicket::class, 'id', 'repair_ticket_id');
    }
}
