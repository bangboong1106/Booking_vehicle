<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\Customer;
use App\Model\Entities\CustomerGroupCustomer;
use App\Model\Entities\Order;
use App\Model\Entities\Routes;
use App\Model\Entities\OrderCustomer;
use App\Repositories\Base\CustomRepository;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ClientRepository extends CustomRepository
{
    protected $_fieldsSearch = ['email', 'mobile_no', 'full_name', 'customer_code'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Customer::class;
    }

    public function validator()
    {
        return \App\Validators\ClientValidator::class;
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    protected function _withRelations($query)
    {
        return $query->with('adminUser');
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
            'c.full_name as name_of_parent_id',
            DB::raw('group_concat(distinct cg.name SEPARATOR \' ; \') as customer_group_name'),
        ];
        $queryBuilder = $this->search($query, $columns);
        return $queryBuilder->paginate($perPage);
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 31/08/2020
    protected function getKeyValue()
    {
        return [
            'name_of_parent_id' => [
                'filter_field' => 'c.full_name',
            ],
            'admin_user_username' => [
                'filter_field' => 'ad.username',
            ],
            'customer_group' => [
                'filter_field' => 'cg.name',
            ],
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
            ->leftJoin('customer as c', $this->getTableName() . '.parent_id', '=', 'c.id')
            ->leftJoin('admin_users as ad', $this->getTableName() . '.user_id', '=', 'ad.id')
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id')
            ->leftJoin('customer_group_customer as cgc', $this->getTableName() . '.id', '=', 'cgc.customer_id')
            ->leftJoin('customer_group as cg', 'cgc.customer_group_id', '=', 'cg.id')
            ->whereNotNull($this->getTableName() . '.parent_id')
            ->where($this->getTableName() . '.customer_type', '=', config('constant.KHACH_HANG'))
            ->orderBy($this->getSortField(), $this->getSortType())
            ->groupBy($this->getTableName() . '.id');
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        $columns = [
            '*',
            'c.full_name as name_of_parent_id'
        ];
        return $this->search($query, $columns)->with(['adminUser'])->paginate($limit, ['*'], 'page', 1);
    }

    public function getExportByID($id)
    {
        $table_name = $this->getTableName();
        $query = DB::table($table_name)
            ->leftJoin('admin_users as ad', $table_name . '.user_id', '=', 'ad.id')
            ->where([
                [$table_name . '.id', '=', $id],
            ]);
        $data = $query->get([
            'customer.*',
            DB::raw('case
			when ' . $table_name . '.type = 1 then "Khách hàng doanh nghiệp"
                else "Khách hàng cá nhân"
                end customer_type'),
            DB::raw('case
			when ' . $table_name . '.sex = "female" then "Nữ"
                else "Nam"
                end sex_type'),
            'ad.username as account_name'
        ])->first();

        $data = $data == null ? new stdClass() : $data;
        return $data;
    }

    // API trả kết quả mobile
    // CreatedBy nlhoang 20/05/2020
    public function getDataByID($id)
    {
        $table_name = $this->getTableName();
        $item = DB::table($table_name)
            ->leftJoin('m_province', $table_name . '.province_id', '=', 'm_province.province_id')
            ->leftJoin('m_district', $table_name . '.district_id', '=', 'm_district.district_id')
            ->leftJoin('m_ward', $table_name . '.ward_id', '=', 'm_ward.ward_id')
            ->leftJoin('files', $table_name . '.avatar_id', '=', 'files.file_id')
            ->leftJoin('admin_users', 'admin_users.id', '=', $table_name . '.user_id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
                'm_province.title as name_of_province_id',
                'm_district.title as name_of_district_id',
                'm_ward.title as name_of_ward_id',
                'files.file_name as name_of_avatar_id',
                'files.file_type',
                'files.path',
                'admin_users.username',
                'admin_users.email',
            ])->first();

        $item->path_of_avatar_id = AppConstant::getImagePath($item->path, $item->file_type);

        return $item;
    }


    // Lấy ra danh sách KH theo ng dùng
    public function getItemsByUserID($all, $q, $userID, $getOnlyGoodsOwner = false)
    {
        $query = Customer::select(
            "customer.id",
            "full_name as title",
            "mobile_no",
            DB::raw("CASE WHEN customer.type = 1 THEN customer.delegate ELSE customer.full_name END as delegate")
        )
            ->leftJoin('customer_group_customer as cgc', function ($join) {
                $join->on('cgc.customer_id', '=', 'customer.id')
                    ->where('cgc.del_flag', '=', 0);
            })->leftJoin('admin_users_customer_group as aucg', function ($join) {
                $join->on('aucg.customer_group_id', '=', 'cgc.customer_group_id')
                    ->where('aucg.del_flag', '=', 0);
            })
            ->where(function ($query) use ($q) {
                $query->where('full_name', 'LIKE', '%' . $q . '%')
                    ->orWhere('mobile_no', 'LIKE', '%' . $q . '%')
                    ->orWhere('customer_code', 'LIKE', '%' . $q . '%');
            });
        if (empty($all)) {
            $query = $query->where(function ($query) use ($userID) {
                $query->where('aucg.admin_user_id', '=', $userID)
                    ->orWhereNull('aucg.customer_group_id');
            });
        }

        if ($getOnlyGoodsOwner) {
            $query = $query->whereNull('customer.parent_id');
        }

        $query = $query->where('customer.del_flag', '=', '0')->distinct()
            ->orderBy('full_name', 'asc')
            ->paginate(10);
        return $query;
    }

    public function getItemsForSheet($userID)
    {
        return Customer::where('del_flag', '=', 0)
            ->orderBy('full_name')
            ->get([
                DB::raw('CONCAT(customer_code,"|", full_name) as name'),
                'id'
            ]);
    }

    // Xử lý gộp trùng KH
    //CreatedBy nlhoang 30/09/2020
    public function processDeduplicate($sourceID, $destinationIDs)
    {
        Order::whereIn('customer_id', $destinationIDs)
            ->update([
                'customer_id' => $sourceID
            ]);
        OrderCustomer::whereIn('customer_id', $destinationIDs)
            ->update([
                'customer_id' => $sourceID
            ]);

        CustomerGroupCustomer::whereIn('customer_id', $destinationIDs)
            ->update([
                'customer_id' => $sourceID
            ]);
        Customer::whereIn('id', $destinationIDs)->delete();
    }

    public function _isUsed($id)
    {
        $orderCustomer = DB::table('order_customer')
            ->where('order_customer.customer_id', '=', $id)
            ->orWhere('order_customer.client_id', '=', $id)
            ->where("order_customer.del_flag", '=', '0')
            ->first();
        if ($orderCustomer) {
            return true;
        }

        $customerDefaultData = DB::table('customer_default_data')
            ->where('customer_default_data.customer_id', '=', $id)
            ->orWhere('customer_default_data.client_id', '=', $id)
            ->where("customer_default_data.del_flag", '=', '0')
            ->first();
        if ($customerDefaultData) {
            return true;
        }

        $order = DB::table('orders')
            ->where('orders.customer_id', '=', $id)
            ->orWhere('orders.client_id', '=', $id)
            ->where("orders.del_flag", '=', '0')
            ->first();

        if ($order) {
            return true;
        }

        return false;
    }

    public function getClientByCustomerId($customerId, $q) {
        $query = Customer::select("id", "full_name as title", "mobile_no", "customer_code")
        ->where(function ($query) use ($q) {
            $query->where('full_name', 'LIKE', '%' . $q . '%')
                ->orWhere('customer_code', 'LIKE', '%' . $q . '%')
                ->orWhere('mobile_no', 'LIKE', '%' . $q . '%');
        })->where([
            ['del_flag', 0],
            ['customer_type', 3]
        ]);

        if ($customerId > 0) {
            $query = $query->where('parent_id', $customerId);
        }

        $query = $query->orderBy('full_name', 'asc')->paginate(20);

        return $query;
    }
}
