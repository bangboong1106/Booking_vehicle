<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use Exception;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property mixed status
 * @property mixed active
 * @property mixed current_location
 */
class Vehicle extends ModelSoftDelete implements Auditable
{
    protected $table = "vehicle";
    protected $fillable = [
        'group_id', 'gps_company_id', 'type', 'reg_no', 'latitude', 'longitude', 'current_location',
        'volume', 'weight', 'length', 'width', 'height', 'status', 'active', 'vehicle_plate', 'gps_id'
        , 'repair_distance', 'repair_date', 'partner_id'
    ];

    protected static $_destroyRelations = [
        'vehicleGroup', 'vehicleGeneralInfo'
    ];

    public $listDriver;

    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'group_id', 'gps_company_id', 'type', 'reg_no', 'current_location',
        'volume', 'weight', 'length', 'width', 'height', 'status', 'active', 'register_year', 'brand', 'repair_distance', 'repair_date'
    ];
    protected $_detailNameField = 'reg_no';

    public function vehicleGroup()
    {
        return $this->hasOne(VehicleGroup::class, 'id', 'group_id');
    }

    public function partner()
    {
        return $this->hasOne(Partner::class, 'id', 'partner_id');
    }

    public function gpsCompany()
    {
        return $this->hasOne(GpsCompany::class, 'id', 'gps_company_id');
    }

    public function vehicleGeneralInfo()
    {
        return $this->hasOne(VehicleGeneralInfo::class, 'vehicle_id', 'id');
    }

    public function drivers()
    {
        return $this->belongsToMany('App\Model\Entities\Driver', 'driver_vehicle', 'vehicle_id', 'driver_id')
            ->wherePivot('del_flag', 0);
    }

    public function repairTickets()
    {
        return $this->hasMany(RepairTicket::class, 'vehicle_id', 'id');
    }

    public function getStatus()
    {
        return config('system.vehicle_status.' . $this->status);
    }

    public function getActive()
    {
        return config('system.vehicle_active.' . $this->active);
    }

    public function getUnit($groupUnit, $unit)
    {
        return config('system.vehicle_unit.' . $groupUnit . '.' . $unit);
    }

    public function getCurrentLocation()
    {
        return $this->current_location;
    }

    public function getType()
    {
        return config('system.vehicle_type.' . $this->type);
    }

    public function generateTags(): array
    {
        $extendData = $this->getExtendData();

        if (empty($extendData['vehicleGeneralInfo'])) {
            return [];
        }
        $data = $extendData['vehicleGeneralInfo'];
        if (isset($data['id'])) {
            $vehicleGeneralInfo = VehicleGeneralInfo::where('id', '=', $data['id'])->where('del_flag', '=', '0')->first();
            $check = isset($vehicleGeneralInfo);

            return [
                'current_register_year' => $check ? $data['register_year'] == $vehicleGeneralInfo->register_year ? '' : $vehicleGeneralInfo->register_year : '',
                'register_year' => $check ? $data['register_year'] == $vehicleGeneralInfo->register_year ? '' : $data['register_year'] : '',
                'current_brand' => $check ? $data['brand'] == $vehicleGeneralInfo->brand ? '' : $vehicleGeneralInfo->brand : '',
                'brand' => $check ? $data['brand'] == $vehicleGeneralInfo->brand ? '' : $data['brand'] : '',
                'current_weight_lifting_system' => $check ? $data['weight_lifting_system'] == $vehicleGeneralInfo->weight_lifting_system ? '' : $vehicleGeneralInfo->weight_lifting_system : '',
                'weight_lifting_system' => $check ? $data['weight_lifting_system'] == $vehicleGeneralInfo->weight_lifting_system ? '' : $data['weight_lifting_system'] : '',
                'current_max_fuel' => $check ? $data['max_fuel'] == $vehicleGeneralInfo->max_fuel ? '' : $vehicleGeneralInfo->max_fuel : '',
                'max_fuel' => $check ? $data['max_fuel'] == $vehicleGeneralInfo->max_fuel ? '' : $data['max_fuel'] : '',
                'max_fuel_with_goods' => $check ? $data['max_fuel_with_goods'] == $vehicleGeneralInfo->max_fuel_with_goods ? '' : $data['max_fuel_with_goods'] : '',
                'current_category_of_barrel' => $check ? $data['category_of_barrel'] == $vehicleGeneralInfo->category_of_barrel ? '' : $vehicleGeneralInfo->category_of_barrel : '',
                'category_of_barrel' => $check ? $data['category_of_barrel'] == $vehicleGeneralInfo->category_of_barrel ? '' : $data['category_of_barrel'] : '',
            ];
        } else {
            return [
                'current_register_year' => '',
                'register_year' => '',
                'current_brand' => '',
                'brand' =>  '',
                'current_weight_lifting_system' =>  '',
                'weight_lifting_system' =>  '',
                'current_max_fuel' => '',
                'max_fuel' =>  '',
                'max_fuel_with_goods' => '',
                'current_category_of_barrel' => '',
                'category_of_barrel' => '',
            ];
        }
    }

    public function transformAudit(array $data): array
    {
        try {
            if (Arr::has($data, 'new_values.status')) {
                $data['old_values']['status'] = empty($data['old_values']['status']) ? '' : config('system.vehicle_status.' . $data['old_values']['status']);
                $data['new_values']['status'] = empty($data['new_values']['status']) ? '' : config('system.vehicle_status.' . $data['new_values']['status']);
            }

            if (Arr::has($data, 'new_values.type')) {
                $data['old_values']['type'] = empty($data['old_values']['type']) ? '' : config('system.vehicle_type.' . $data['old_values']['type']);
                $data['new_values']['type'] = empty($data['new_values']['type']) ? '' : config('system.vehicle_type.' . $data['new_values']['type']);
            }

            if (Arr::has($data, 'new_values.active')) {
                $data['old_values']['active'] = empty($data['old_values']['active']) ? '' : config('system.vehicle_active.' . $data['old_values']['active']);
                $data['new_values']['active'] = empty($data['new_values']['active']) ? '' : config('system.vehicle_active.' . $data['new_values']['active']);
            }

            if (Arr::has($data, 'new_values.group_id')) {
                $data['old_values']['group_id'] = empty($data['old_values']['group_id']) ? '' : VehicleGroup::find($this->getOriginal('group_id'))->name;
                $data['new_values']['group_id'] = empty($data['new_values']['group_id']) ? '' : VehicleGroup::find($this->getAttribute('group_id'))->name;
            }

            $data = $this->checkFloatValue($data, 'volume');
            $data = $this->checkFloatValue($data, 'weight');
            $data = $this->checkFloatValue($data, 'width');
            $data = $this->checkFloatValue($data, 'length');
            $data = $this->checkFloatValue($data, 'height');

            if (isset($data['tags'])) {
                $extendData = explode(',', $data['tags']);

                if (!empty($extendData[0]) || !empty($extendData[1])) {
                    $data['old_values']['register_year'] = $extendData[0];
                    $data['new_values']['register_year'] = $extendData[1];
                }
                if (!empty($extendData[2]) || !empty($extendData[3])) {
                    $data['old_values']['brand'] = $extendData[2];
                    $data['new_values']['brand'] = $extendData[3];
                }
                if (!empty($extendData[4]) || !empty($extendData[5])) {
                    $data['old_values']['weight_lifting_system'] = $extendData[4];
                    $data['new_values']['weight_lifting_system'] = $extendData[5];
                }
                if (!empty($extendData[6]) || !empty($extendData[7])) {
                    $data['old_values']['max_fuel'] = $extendData[6];
                    $data['new_values']['max_fuel'] = $extendData[7];
                }
                if (!empty($extendData[8]) || !empty($extendData[9])) {
                    $data['old_values']['category_of_barrel'] = $extendData[8];
                    $data['new_values']['category_of_barrel'] = $extendData[9];
                }
            }
        } catch (Exception $e) {
            logError($e->getMessage());
        }

        return $data;
    }
}
