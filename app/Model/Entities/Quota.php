<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Exception;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property float|int total_cost
 * @property string title
 * @property string location_ids
 * @property int location_destination_id
 * @property int location_arrival_id
 * @property mixed id
 * @property mixed vehicle_group_id
 */
class Quota extends ModelSoftDelete implements Auditable
{
    protected $table = "quota";

    protected $_alias = 'quota';
    protected $fillable = ['quota_code', 'name', 'vehicle_group_id', 'title', 'location_ids', 'location_destination_id',
        'location_arrival_id', 'total_cost', 'distance', 'location_destination_group_id', 'location_arrival_group_id'];

    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = ['quota_code', 'name', 'vehicle_group_id', 'title', 'location_destination_id', 'location_arrival_id',
        'total_cost', 'distance', 'location_destination_group_id', 'location_arrival_group_id'];
    protected $_detailNameField = 'quota_code';

    protected static $_destroyRelations = ['vehicleGroup'];

    public function vehicleGroup()
    {
        return $this->hasOne(VehicleGroup::class, 'id', 'vehicle_group_id');
    }

    public function costs()
    {
        return $this->hasMany(QuotaCost::class, 'quota_id', 'id');
    }

    public function locations()
    {
        return $this->belongsToMany('App\Model\Entities\Location', 'quota_location',
            'quota_id', 'location_id')
            ->wherePivotIn('location_order', [0, 1]);
    }

    public function costList()
    {
        return $this->belongsToMany('App\Model\Entities\ReceiptPayment', 'quota_cost',
            'quota_id', 'receipt_payment_id')
            ->where('type', '=', config('constant.COST'));
    }

    public function transformAudit(array $data): array
    {
        try {
            if (Arr::has($data, 'new_values.location_destination_id')) {
                $data['old_values']['location_destination_id'] = empty($data['old_values']['location_destination_id']) ? '' : Location::find($this->getOriginal('location_destination_id'))->title;
                $data['new_values']['location_destination_id'] = empty($data['new_values']['location_destination_id']) ? '' : Location::find($this->getAttribute('location_destination_id'))->title;
            }

            if (Arr::has($data, 'new_values.location_arrival_id')) {
                $data['old_values']['location_arrival_id'] = empty($data['old_values']['location_arrival_id']) ? '' : Location::find($this->getOriginal('location_arrival_id'))->title;
                $data['new_values']['location_arrival_id'] = empty($data['new_values']['location_arrival_id']) ? '' : Location::find($this->getAttribute('location_arrival_id'))->title;
            }

            if (Arr::has($data, 'new_values.vehicle_group_id')) {
                $data['old_values']['vehicle_group_id'] = empty($data['old_values']['vehicle_group_id']) ? '' : VehicleGroup::find($this->getOriginal('vehicle_group_id'))->name;
                $data['new_values']['vehicle_group_id'] = empty($data['new_values']['vehicle_group_id']) ? '' : VehicleGroup::find($this->getAttribute('vehicle_group_id'))->name;
            }

            $data = $this->checkFloatValue($data, 'total_cost', true);
        } catch (Exception $e) {
            logError($e->getMessage());
        }
        return $data;
    }
}