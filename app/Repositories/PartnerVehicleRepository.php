<?php

namespace App\Repositories;

use App\Model\Entities\Vehicle;
use App\Repositories\Base\CustomRepository;
use App\Repositories\Traits\VehicleExportTrait;
use App\Validators\PartnerVehicleValidator;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PartnerVehicleRepository extends CustomRepository
{
    use VehicleExportTrait;

    protected $_fieldsSearch = ['reg_no', 'current_location'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Vehicle::class;
    }

    public function validator()
    {
        return PartnerVehicleValidator::class;
    }

    public function getCode()
    {
        return 'reg_no';
    }

    /**
     * @param QueryBuilder $query
     * @return mixed
     */
    protected function _withRelations($query)
    {
        return $query->with('vehicleGroup', 'vehicleGeneralInfo');
    }

    public function getListForHistory($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));

        $queryBuilder = $this->search($query);

        return $this->_withRelations($queryBuilder)->paginate($perPage);
    }


    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
            DB::raw('CONCAT(\'<div class="list-tag-column">\', GROUP_CONCAT(distinct CONCAT(\'<span class="grid-tag">\', (driver.full_name),\'</span>\') SEPARATOR \'\'), \'</div>\') as drivers_name ')
        ];
        $queryBuilder = $this->search($query, $columns);
        return $queryBuilder->paginate($perPage);
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 31/08/2020
    protected function getKeyValue()
    {
        return [
            'drivers_name' => [
                'filter_field' => 'driver.full_name',
            ],
            'name_of_gps_company_id' => [
                'filter_field' => 'vg.name',
            ],
            'name_of_group_id' => [
                'filter_field' => 'vg.name',
            ],
            'name_of_ins_id' => [
                'filter_field' => 'ai.username'
            ],
            'name_of_upd_id' => [
                'filter_field' => 'au.username',
            ]
        ];
    }

    // Hàm build câu lệnh tài xế
    // CreatedBy nlhoang 04/09/2020
    protected function getQueryBuilder($columns)
    {
        $user = Auth::user();
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('vehicle_general_info as vgi', $this->getTableName() . '.id', '=', 'vgi.vehicle_id')
            ->leftJoin('driver_vehicle as dv', 'dv.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('drivers as driver', 'driver.id', '=', 'dv.driver_id')
            ->leftJoin('m_vehicle_group as vg', 'vg.id', '=', $this->getTableName() . '.group_id')
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id')
            ->where($this->getTableName() . '.partner_id', $user->partner_id)
            ->groupBy('vehicle.id')
            ->orderBy($this->getSortField(), $this->getSortType());
    }

}
