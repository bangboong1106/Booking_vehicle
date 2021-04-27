<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\CustomerDefaultData;
use App\Repositories\Base\CustomRepository;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomerDefaultDataRepository extends CustomRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return CustomerDefaultData::class;
    }

    public function validator()
    {
        return \App\Validators\CustomerDefaultDataValidator::class;
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    protected function _withRelations($query)
    {
        return $query->with('customer', 'systemCodeConfig');
    }


    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
        ];
        $queryBuilder = $this->search($query, $columns)->with('customer', 'systemCodeConfig');
        return $queryBuilder->paginate($perPage);
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 08/09/2020
    protected function getKeyValue()
    {
        return [
            'name_of_customer_id' => [
                'filter_field' => 'c.full_name',
            ],
            'name_of_system_code_config_id' => [
                'filter_field' => 'scc.prefix',
            ],
        ];
    }

    // Hàm build câu lệnh khách hàng
    // CreatedBy nlhoang 08/09/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('customer as c', $this->getTableName() . '.customer_id', '=', 'c.id')
            ->leftJoin('system_code_config as scc', $this->getTableName() . '.system_code_config_id', '=', 'scc.id')
            ->orderBy($this->getSortField(), $this->getSortType());
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        return $this->search($query)->with(['customer', 'systemCodeConfig'])->paginate($limit, ['*'], 'page', 1);
    }

    // Hàm lấy dữ liệu mặc định KH
    // CreatedBy nlhoang 08/09/2020
    public function getDefaultDataByCustomerID($customerID)
    {
        return $this->search([
            'customer_id_eq' => $customerID
        ], [])->get();
    }
}
