<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;

use App\Model\Entities\AdminUsersCustomerGroup;
use App\Model\Entities\CustomerGroup;
use App\Model\Entities\CustomerGroupCustomer;
use App\Repositories\Base\CustomRepository;
use App\Validators\CustomerGroupValidator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerGroupRepository extends CustomRepository
{
    function model()
    {
        return CustomerGroup::class;
    }

    public function validator()
    {
        return CustomerGroupValidator::class;
    }

    protected function _prepareRelation($entity, $data, $forUpdate = false)
    {
        $entity = parent::_prepareRelation($entity, $data, $forUpdate);
        $entity->customer_ids = isset($data['customer_ids']) ? $data['customer_ids'] : null;
        return $entity;
    }

    public function getListForSelect()
    {
        return $this->search([
            'sort_type' => 'asc',
            'sort_field' => 'name'
        ])->get()->pluck('name', 'id');
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
            DB::raw('CONCAT(\'<div class="list-tag-column">\', GROUP_CONCAT(distinct CONCAT(\'<span class="grid-tag">\', (c.full_name),\'</span>\') SEPARATOR \'\'), \'</div>\') as customer_names ')
        ];
        $queryBuilder = $this->search($query, $columns);
        return $queryBuilder->paginate($perPage);
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 31/08/2020
    protected function getKeyValue()
    {
        return [
            'customer_name' => [
                'filter_field' => 'c.full_name',
            ]
        ];
    }

    // Hàm build câu lệnh tài xế
    // CreatedBy nlhoang 31/08/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('customer_group_customer as cgc', 'cgc.customer_group_id', '=', 'customer_group.id')
            ->leftJoin('customer as c', 'c.id', '=', 'cgc.customer_id')
            ->orderBy($this->getSortField(), $this->getSortType())
            ->groupBy('customer_group.id');
    }

    public function getAdminUsersCustomerGroups($id)
    {
        if (empty($id)) {
            return [];
        }

        return AdminUsersCustomerGroup::where('del_flag', '=', 0)
            ->where('customer_group_id', '=', $id)->get();
    }

    public function getCustomerGroupCustomers($id)
    {
        if (empty($id)) {
            return [];
        }

        return CustomerGroupCustomer::where('del_flag', '=', 0)
            ->where('customer_group_id', '=', $id)->get();
    }

    public function getItemsByUserID($all, $q, $user_id)
    {
        $query = CustomerGroup::select("customer_group.id", "customer_group.name as title", "customer_group.code as code")
            ->where('customer_group.code', 'LIKE', '%' . $q . '%')
            ->orWhere('customer_group.name', 'LIKE', '%' . $q . '%')
            ->orderBy('customer_group.code', 'asc')
            ->paginate(10);
        return $query;
    }
}
