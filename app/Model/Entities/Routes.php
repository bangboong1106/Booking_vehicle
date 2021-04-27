<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Exception;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property mixed route_status
 * @property mixed is_approved
 * @property float|int|mixed capacity_weight_ratio
 * @property float|int|mixed capacity_volume_ratio
 * @property mixed orders
 * @property mixed vehicle
 */
class Routes extends ModelSoftDelete implements Auditable
{
    protected $table = "routes";

    protected $_alias = 'route';

    protected $fillable = [
        'route_code',
        'name',
        'ETD_date',
        'ETD_time',
        'ETD_date_reality',
        'ETD_time_reality',
        'ETA_date',
        'ETA_time',
        'ETA_date_reality',
        'ETA_time_reality',
        'route_status',
        'quota_id',
        'vehicle_id',
        'driver_id',
        'final_cost',
        'route_note',
        'is_approved',
        'approved_id',
        'approved_date',
        'approved_note',
        'gps_distance',
        'capacity_weight_ratio',
        'capacity_volume_ratio',
        'price_quote_amount',
        'payroll_amount',
        'gps_distance',
        'location_destination_id',
        'location_arrival_id',
        'order_codes',
        'customer_ids',
        'volume',
        'weight',
        'quantity',
        'total_amount',
        'count_order',
        'vin_nos',
        'model_nos',
        'is_lock',
        'order_notes',
        'partner_id'
    ];

    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'route_code',
        'name',
        'ETD_date',
        'ETD_time',
        'ETD_date_reality',
        'ETD_time_reality',
        'ETA_date',
        'ETA_time',
        'ETA_date_reality',
        'ETA_time_reality',
        'route_status',
        'quota_id',
        'vehicle_id',
        'driver_id',
        'final_cost',
        'route_note',
        'is_approved',
        'approved_id',
        'approved_date',
        'approved_note',
        'gps_distance',
        'capacity_weight_ratio',
        'capacity_volume_ratio',
        'price_quote_amount',
        'payroll_amount',
        'gps_distance',
        'location_destination_id',
        'location_arrival_id',
        'order_codes',
        'customer_ids',
        'volume',
        'weight',
        'quantity',
        'total_amount',
        'count_order',
        'vin_nos',
        'model_nos',
        'is_lock',
        'order_notes',
        'partner_id'
    ];
    public $listCost;
    protected $_detailNameField = 'route_code';

    public function driver()
    {
        return $this->hasOne(Driver::class, 'id', 'driver_id');
    }

    public function vehicle()
    {
        return $this->hasOne(Vehicle::class, 'id', 'vehicle_id');
    }

    public function quota()
    {
        return $this->hasOne(Quota::class, 'id', 'quota_id');
    }

    public function costs()
    {
        return $this->hasMany(RouteCost::class, 'route_id');
    }

    public function orders()
    {
        return $this->belongsToMany(
            'App\Model\Entities\Order',
            'route_order',
            'route_id',
            'order_id'
        )->wherePivot('del_flag', '=', config('system.del_flag_column.active'));
    }

    public function partner()
    {
        return $this->hasOne(Partner::class, 'id', 'partner_id');
    }

    public function transformAudit(array $data): array
    {
        try {
            if (Arr::has($data, 'new_values.quota_id')) {
                $data['old_values']['quota_id'] = empty($data['old_values']['quota_id']) ? '' : Quota::find($this->getOriginal('quota_id'))->name;
                $data['new_values']['quota_id'] = empty($data['new_values']['quota_id']) ? '' : Quota::find($this->getAttribute('quota_id'))->name;
            }

            if (Arr::has($data, 'new_values.vehicle_id')) {
                $data['old_values']['vehicle_id'] = empty($data['old_values']['vehicle_id']) ? '' : Vehicle::find($this->getOriginal('vehicle_id'))->reg_no;
                $data['new_values']['vehicle_id'] = empty($data['new_values']['vehicle_id']) ? '' : Vehicle::find($this->getAttribute('vehicle_id'))->reg_no;
            }

            if (Arr::has($data, 'new_values.driver_id')) {
                $data['old_values']['driver_id'] = empty($data['old_values']['driver_id']) ? '' : Driver::find($this->getOriginal('driver_id'))->full_name;
                $data['new_values']['driver_id'] = empty($data['new_values']['driver_id']) ? '' : Driver::find($this->getAttribute('driver_id'))->full_name;
            }
        } catch (Exception $e) {
            logError($e->getMessage());
        }

        return $data;
    }

    public function getStatus()
    {
        $statusList = config('system.route_status');
        return array_key_exists($this->route_status, $statusList) ? $statusList[$this->route_status] : '-';
    }

    public function getIsApproved()
    {
        $isApprovedList = config('system.route_is_approved');
        return array_key_exists($this->is_approved, $isApprovedList) ? $isApprovedList[$this->is_approved] : '-';
    }

    public function approvedUser()
    {
        return $this->hasOne(AdminUserInfo::class, 'id', 'approved_id');
    }

    public function locationDestination()
    {
        return $this->hasOne(Location::class, 'id', 'location_destination_id');
    }

    public function locationArrival()
    {
        return $this->hasOne(Location::class, 'id', 'location_arrival_id');
    }

    public function getStatusOnList()
    {
        $class = "secondary";
        if ($this->route_status == 1) {
            $class = "success";
        }
        if ($this->route_status == 2) {
            $class = "danger";
        }
        return '<span class="badge badge-' . $class . '">' . $this->getStatus() . "</span>";
    }

    public function save(array $options = [])
    {
        parent::save($options);
    }
}
