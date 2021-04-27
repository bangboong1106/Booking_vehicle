<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Base\NestedSetBase;
use App\Model\Entities\Province;
use App\Model\Entities\VehicleTeam;
use App\Repositories\Base\CustomRepository;
use App\Validators\ProvinceValidator;
use App\Validators\VehicleTeamValidator;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VehicleTeamRepository extends CustomRepository
{
    function model()
    {
        return VehicleTeam::class;
    }

    public function validator()
    {
        return VehicleTeamValidator::class;
    }

    public function getListForSelect()
    {
        $partnerId = Auth::user()->partner_id;
        $params = [
            'sort_type' => 'asc',
            'sort_field' => 'name'
        ];

        if ($partnerId && $partnerId != 0) {
            $params['partner_id_eq'] = $partnerId;
        }
        return $this->search($params)->get()->pluck('name', 'id');
    }

    protected function _prepareRelation($entity, $data, $forUpdate = false)
    {
        $entity = parent::_prepareRelation($entity, $data, $forUpdate);
        $entity->driver_ids = isset($data['driver_ids']) ? $data['driver_ids'] : null;
        return $entity;
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
            'capital_driver.full_name as capital_driver_full_name',
            DB::raw('CONCAT(\'<div class="list-tag-column">\', GROUP_CONCAT(distinct CONCAT(\'<span class="grid-tag">\', (vehicle.reg_no),\'</span>\') SEPARATOR \'\'), \'</div>\') as vehicles_reg_no '),
            DB::raw('CONCAT(\'<div class="list-tag-column">\', GROUP_CONCAT(distinct CONCAT(\'<span class="grid-tag">\', (driver.full_name),\'</span>\') SEPARATOR \'\'), \'</div>\') as drivers_name ')
        ];
        $queryBuilder = $this->search($query, $columns);
        return $queryBuilder->paginate($perPage);
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 03/09/2020
    protected function getKeyValue()
    {
        return [
            'name_of_capital_driver_id' => [
                'filter_field' => 'capital_driver.full_name',
            ],
            'drivers_name' => [
                'filter_field' => 'driver.full_name',
            ],
            'vehicles_reg_no' => [
                'filter_field' => 'vehicle.reg_no',
            ],
            'name_of_partner_id' => [
                'filter_field' => 'p.full_name',
            ]
        ];
    }

    // Hàm build câu lệnh đội tài xế
    // CreatedBy nlhoang 03/09/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('drivers as capital_driver', 'vehicle_team.capital_driver_id', '=', 'capital_driver.id')
            ->leftJoin('driver_vehicle_team as dvh', 'dvh.vehicle_team_id', '=', 'vehicle_team.id')
            ->leftJoin('driver_vehicle as dv', 'dv.driver_id', '=', 'dvh.driver_id')
            ->leftJoin('vehicle as vehicle', 'vehicle.id', '=', 'dv.vehicle_id')
            ->leftJoin('drivers as driver', 'driver.id', '=', 'dvh.driver_id')
            ->leftJoin('partner as p', $this->getTableName() . '.partner_id', '=', 'p.id')
            ->groupBy('vehicle_team.id')
            ->orderBy($this->getSortField(), $this->getSortType());
    }

    public function getVehiclesByID($vehicleTeamId)
    {
        if (empty($vehicleTeamId)) {
            return null;
        }
        return DB::table('vehicle as v')
            ->leftJoin('m_vehicle_group as vg', 'vg.id', '=', 'v.group_id')
            ->leftJoin('driver_vehicle as dv', 'dv.vehicle_id', '=', 'v.id')
            ->leftJoin('driver_vehicle_team as dvt', 'dvt.driver_id', '=', 'dv.driver_id')
            ->where([
                ['dvt.vehicle_team_id', '=', $vehicleTeamId],
                ['v.del_flag', '=', 0],

            ])
            ->distinct()
            ->get(['v.id', 'v.reg_no', 'vg.name']);
    }

    public function getIDsByCodes($codes)
    {
        if (empty($codes)) {
            return [];
        }

        return $this->search(['code_in' => $codes], ['id'])->pluck('id')->toArray();
    }

    public function getItemsByUserID($q, $partner_id)
    {
        $query = VehicleTeam::select("vehicle_team.id", "vehicle_team.name as title", "vehicle_team.code as code", "drivers.full_name as capital_driver")
            ->leftJoin('drivers', 'drivers.id', '=', 'vehicle_team.capital_driver_id')
            ->where(function ($query) use ($q) {
                $query->where('vehicle_team.code', 'LIKE', '%' . $q . '%')
                    ->orWhere('vehicle_team.name', 'LIKE', '%' . $q . '%')
                    ->orderBy('vehicle_team.code', 'asc');
            });
        if (isset($partner_id) && $partner_id > 0) {
            $query->where('vehicle_team.partner_id', '=', $partner_id);
        }
        return $query->paginate(10);
    }

    public function getVehicleTeamByPartnerId($partnerId)
    {
        return $this->search([
            'partner_id_eq' => $partnerId,
        ])->get();
    }
}
