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

class CustomerRepository extends CustomRepository
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
        return \App\Validators\CustomerValidator::class;
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    protected function _withRelations($query)
    {
        return $query->with('adminUser');
    }

    public function getCustomerInfo($customerID)
    {
        $table_name = $this->getTableName();
        $items = DB::table($table_name)
            ->leftJoin('m_province', $table_name . '.province_id', '=', 'm_province.province_id')
            ->leftJoin('m_district', $table_name . '.district_id', '=', 'm_district.district_id')
            ->leftJoin('m_ward', $table_name . '.ward_id', '=', 'm_ward.ward_id')
            ->leftJoin('files', $table_name . '.avatar_id', '=', 'files.file_id')
            ->leftJoin('admin_users', 'admin_users.id', '=', $table_name . '.user_id')
            ->where($table_name . '.id', '=', $customerID)
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
            ]);
        foreach ($items as $item) {
            $item->path_of_avatar_id = AppConstant::getImagePath($item->path, $item->file_type);
        }
        return $items;
    }

    public function updateField($params, $customerID)
    {
        Customer::where('id', $customerID)->update(array($params["fieldName"] => $params["value"]));
    }

    public function getForSelect()
    {
        return $this->search()->pluck('full_name', 'id');
    }

    public function getCustomerByUserId($userId)
    {
        $entity = null;
        if ($userId) {
            $entity = Customer::where('user_id', '=', $userId)->first();
        }
        return $entity;
    }


    public function getCustomerByParentId($client)
    {
        $entity = $client;
        if (!empty($entity->parent_id)) {
            $entity = Customer::where('id', '=', $entity->parent_id)->first();
        }
        return $entity;
    }

    public function getCustomerTypeByUserId($userId)
    {
        $entity = null;
        if ($userId) {
            $entity = Customer::where('user_id', '=', $userId)->first();
            if ($entity != null && $entity->parent_id != null) {
                $entity = Customer::where('id', '=', $entity->parent_id)->first();
            }
        }
        return $entity;
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
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
            ->leftJoin('admin_users as ad', $this->getTableName() . '.user_id', '=', 'ad.id')
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id')
            ->leftJoin('customer_group_customer as cgc', $this->getTableName() . '.id', '=', 'cgc.customer_id')
            ->leftJoin('customer_group as cg', 'cgc.customer_group_id', '=', 'cg.id')
            ->whereNull('parent_id')
            ->orderBy($this->getSortField(), $this->getSortType())
            ->groupBy($this->getTableName() . '.id');
    }

    public function getListUserIdByIds($ids)
    {
        if (empty($ids)) {
            return [];
        }
        return $this->search([
            'id_in' => $ids,
        ])->pluck('user_id');
    }

    public function getCustomerList($request)
    {
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $sorts = $request['sort'];

        $queryCount = DB::table('drivers')
            ->where([
                ['drivers.del_flag', '=', '0'],
                ['drivers.active', '=', '1']
            ]);

        $customersQuery = DB::table('customer')
            ->where([
                ['customer.del_flag', '=', '0'],
                ['customer.active', '=', '1']
            ]);

        if (!empty($textSearch)) {
            $queryCount->where(function ($query) use ($textSearch) {
                $query->where('customer.customer_code', 'like', '%' . $textSearch . '%')
                    ->orWhere('customer.mobile_no', 'like', '%' . $textSearch . '%')
                    ->orWhere('customer.full_name', 'like', '%' . $textSearch . '%');
            });
            $customersQuery->where(function ($query) use ($textSearch) {
                $query->where('customer.customer_code', 'like', '%' . $textSearch . '%')
                    ->orWhere('customer.mobile_no', 'like', '%' . $textSearch . '%')
                    ->orWhere('customer.full_name', 'like', '%' . $textSearch . '%');
            });
        }

        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                $customersQuery->orderBy($sort['sortField'], $sort['sortType']);
            }
        }

        $count = $queryCount->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $customersQuery->skip($offset)->take($pageSize);
        $customers = $customersQuery->get();
        return [
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'items' => $customers
        ];
    }

    public function findByFullName($fullName)
    {
        // Check theo tên khách hàng và mã hệ thống
        if (empty($fullName)) {
            return [];
        }
        $customersQuery = DB::table('customer')
            ->where([
                ['customer.del_flag', '=', '0'],
                ['customer.full_name', '=', $fullName]
            ])
            ->orWhere([
                ['customer.del_flag', '=', '0'],
                ['customer.customer_code', '=', $fullName]
            ]);

        return $customersQuery->get()->first();
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        return $this->search($query)->with(['adminUser'])->paginate($limit, ['*'], 'page', 1);
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

    public function getAllCustomerByRole()
    {
        $table_name = $this->getTableName();
        return DB::table($table_name)
            ->leftJoin('customer_group_customer as cgc', function ($join) {
                $join->on('cgc.customer_id', '=', 'customer.id')
                    ->where('cgc.del_flag', '=', 0);
            })
            ->leftJoin('admin_users_customer_group as aucg', function ($join) {
                $join->on('aucg.customer_group_id', '=', 'cgc.customer_group_id')
                    ->where('aucg.del_flag', '=', 0);
            })
            ->where(function ($query) {
                $query->where('aucg.admin_user_id', '=', Auth::User()->id)
                    ->orWhereNull('aucg.customer_group_id');
            })
            ->where($table_name . '.del_flag', '=', 0)
            ->groupBy('customer.id')
            ->get([
                'customer.id', 'customer_code', 'full_name', 'delegate', 'mobile_no', 'type'
            ])->sortBy('full_name');
    }

    public function getCustomerGroups($id)
    {
        if (!$id)
            return null;
        return DB::table('customer_group_customer as cgc')
            ->leftJoin('customer_group as cg', 'cg.id', '=', 'cgc.customer_group_id')
            ->leftJoin('admin_users_customer_group as aucg', 'aucg.customer_group_id', '=', 'cgc.customer_group_id')
            ->leftJoin('admin_users as au', 'au.id', '=', 'aucg.admin_user_id')
            ->where('cgc.customer_id', '=', $id)
            ->groupBy('cg.id')
            ->get([
                'cg.id', 'cg.name',
                DB::raw('group_concat(au.username SEPARATOR \' , \') as username')
            ]);
    }

    public function getCustomerCodeByIds($ids = [])
    {
        if (empty($ids)) {
            return [];
        }
        return $this->search(['id_in' => $ids], ['id', 'customer_code'])->get();
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

    public function getCustomerOfGoodsOwner($id)
    {
        $table_name = $this->getTableName();
        return DB::table($table_name)
            ->leftJoin('admin_users as ad', $this->getTableName() . '.user_id', '=', 'ad.id')
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id')
            ->where('parent_id', $id)
            ->where('customer_type','=', config('constant.KHACH_HANG'))
            ->select(
                $this->getTableName() . '.*',
                'ai.username as ai_username',
                'au.username as au_username'
            )
            ->get();
    }

    public function getGoodsOwnerList()
    {
        return Customer::where('del_flag', '=', 0)
            ->whereNull('parent_id')
            ->orderBy('full_name')
            ->get();
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
}
