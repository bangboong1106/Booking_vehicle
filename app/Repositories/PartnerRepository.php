<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\AdminUserInfo;
use App\Model\Entities\Customer;
use App\Model\Entities\Partner;
use App\Repositories\Base\CustomRepository;
use App\Validators\PartnerValidator;
use DB;
use Illuminate\Database\Eloquent\Builder;

class PartnerRepository extends CustomRepository
{
    protected $_fieldsSearch = ['mobile_no', 'full_name', 'code'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Partner::class;
    }

    public function validator()
    {
        return PartnerValidator::class;
    }

    public function updateField($params, $customerID)
    {
        Partner::where('id', $customerID)->update(array($params["fieldName"] => $params["value"]));
    }

    public function getListForSelect()
    {
        return $this->search([
            'sort_type' => 'asc',
            'sort_field' => 'full_name'
        ])->get()->pluck('full_name', 'id');
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = ['*'];
        $queryBuilder = $this->search($query, $columns);
        return $this->_withRelations($queryBuilder)->paginate($perPage);

    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 31/08/2020
    protected function getKeyValue()
    {
        return [
            'name_of_ins_id' => [
                'filter_field' => 'ai.username'
            ],
            'name_of_upd_id' => [
                'filter_field' => 'au.username',
            ]
        ];
    }

    // Hàm build câu lệnh khách hàng
    // CreatedBy nlhoang 31/08/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id')
            ->orderBy($this->getSortField(), $this->getSortType())
            ->groupBy($this->getTableName() . '.id');
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        return $this->search($query)->paginate($limit, ['*'], 'page', 1);
    }

    protected function _withRelations($query)
    {
        return $query->with('haveStaff');
    }

    public function _isUsed($id)
    {
        if (AdminUserInfo::where('partner_id', $id)->exists()) {
            return true;
        }

        return false;
    }

    public function getItemsForComboBox($all, $q, $customerId)
    {
        $query = Partner::select("id", "full_name", "code")
                ->where(function ($query) use ($q) {
                    $query->where('code', 'LIKE', '%' . $q . '%')
                        ->orWhere('full_name', 'LIKE', '%' . $q . '%');
                })
                ->orderBy('code', 'asc')
                ->paginate(10);

        return $query;
    }
}
