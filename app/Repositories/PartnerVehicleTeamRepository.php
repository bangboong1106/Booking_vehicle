<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Entities\VehicleTeam;
use App\Repositories\Base\CustomRepository;
use App\Validators\PartnerVehicleTeamValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartnerVehicleTeamRepository extends CustomRepository
{
    function model()
    {
        return VehicleTeam::class;
    }

    public function validator()
    {
        return PartnerVehicleTeamValidator::class;
    }

    public function getListForSelect()
    {
        return $this->search([
            'sort_type' => 'asc',
            'sort_field' => 'name'
        ])->get()->pluck('name', 'id');
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
            ]
        ];
    }

    // Hàm build câu lệnh đội tài xế
    // CreatedBy nlhoang 03/09/2020
    protected function getQueryBuilder($columns)
    {
        $user = Auth::user();
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('drivers as capital_driver', 'vehicle_team.capital_driver_id', '=', 'capital_driver.id')
            ->leftJoin('driver_vehicle_team as dvh', 'dvh.vehicle_team_id', '=', 'vehicle_team.id')
            ->leftJoin('driver_vehicle as dv', 'dv.driver_id', '=', 'dvh.driver_id')
            ->leftJoin('vehicle as vehicle', 'vehicle.id', '=', 'dv.vehicle_id')
            ->leftJoin('drivers as driver', 'driver.id', '=', 'dvh.driver_id')
            ->where($this->getTableName() . '.partner_id', $user->partner_id)
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
}
