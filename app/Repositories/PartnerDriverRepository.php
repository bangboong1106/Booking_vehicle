<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\Driver;
use App\Model\Entities\Routes;
use App\Repositories\Base\CustomRepository;
use App\Repositories\Traits\DriverExportTrait;

use App\Validators\PartnerDriverValidator;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PartnerDriverRepository extends CustomRepository
{
    use DriverExportTrait;

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Driver::class;
    }

    public function validator()
    {
        return PartnerDriverValidator::class;
    }

    protected function _withRelations($query)
    {
        return $query->with('adminUser', 'vehicleTeams');
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        $columns = [
            '*',
            DB::raw('GROUP_CONCAT(distinct CONCAT(vt.code,"|",vt.name) SEPARATOR \',\') as vehicle_team_codes')
        ];

        $queryBuilder = $this->search($query, $columns);
        return $this->_withRelations($queryBuilder)->paginate($limit, ['*'], 'page', 1);
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
            DB::raw('CONCAT(\'<div class="list-tag-column">\', GROUP_CONCAT(distinct CONCAT(\'<span class="grid-tag">\', (vehicle.reg_no),\'</span>\') SEPARATOR \'\'), \'</div>\') as vehicles_reg_no '),
            DB::raw('CONCAT(\'<div class="list-tag-column">\', GROUP_CONCAT(distinct CONCAT(\'<span class="grid-tag">\', (vt.name),\'</span>\') SEPARATOR \'\'), \'</div>\') as vehicle_team_names ')

        ];
        $queryBuilder = $this->search($query, $columns)->with(['insUser', 'updUser', 'adminUser']);
        return $queryBuilder->paginate($perPage);
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 31/08/2020
    protected function getKeyValue()
    {
        return [
            'admin_user_username' => [
                'filter_field' => 'ad.username',
            ],
            'username' => [
                'filter_field' => 'ad.username',
            ],
            'full_name' => [
                'filter_field' => 'drivers.full_name',
            ],
            'name_of_vehicle_team_id' => [
                'filter_field' => 'vt.name',
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
    // CreatedBy nlhoang 31/08/2020
    protected function getQueryBuilder($columns)
    {
        $user = Auth::user();
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('admin_users as ad', $this->getTableName() . '.user_id', '=', 'ad.id')
            ->leftJoin('driver_vehicle_team as dvt', $this->getTableName() . '.id', '=', 'dvt.driver_id')
            ->leftJoin('vehicle_team as vt', 'dvt.vehicle_team_id', '=', 'vt.id')
            ->leftJoin('driver_vehicle as dv', 'dv.driver_id', '=', 'drivers.id')
            ->leftJoin('vehicle as vehicle', 'vehicle.id', '=', 'dv.vehicle_id')
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id')
            ->where($this->getTableName() . '.partner_id', $user->partner_id)
            ->groupBy('drivers.id')
            ->orderBy($this->getSortField(), $this->getSortType());
    }

    /**
     * @param $data
     * @param bool $forUpdate
     * @return mixed
     */
    public function findFirstOrNew($data, $forUpdate = false)
    {
        if (isset($data['create_account']) && $data['create_account'] == 0 && isset($data['adminUser'])) {
            unset($data['adminUser']);
        }

        return parent::findFirstOrNew($data, $forUpdate);
    }

    // Lấy dữ liệu Xe để xuất theo mẫu
    // CreatedBy nlhoang 10/04/2020
    public function getExportByIDs($ids, $parameter, $template)
    {
        return $this->getDataForTemplateByID($ids, $parameter, $template);
    }

}
