<?php

namespace App\Repositories\Client;

use App\Common\AppConstant;
use App\Model\Entities\Customer;
use App\Model\Entities\AdminUserInfo;
use \App\Repositories\CustomerRepository;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class CustomerClientRepository extends CustomerRepository
{
    protected function getWhereTextSearch($query, $table_name, $textSearch)
    {
        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.customer_code', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.full_name', 'like', '%' . $textSearch . '%');
            });
        }
        return $query;
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

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {
        $item = Customer::whereIn('id', $id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid location id: ' . $id);
        }
    }

    // API lưu đối tượng
    // CreatedBy nlhoang 28/05/2020
    public function preSaveEntity($entity, $parameters)
    {
        return $entity;
    }

    // API lưu thêm thông tin tài khoản
    // CreatedBy nlhoang 28/05/2020
    // Modified by ptly 16/07/2020: không cập nhật password.
    public function saveRelationEntity($entity, $parameters)
    {
        $user = AdminUserInfo::where('id', $entity->user_id)->first();
        $user->update([
            'username' => $parameters['username'],
            'full_name' => $entity->full_name,
            'email' => $parameters['email'],
        ]);
    }

    // API lấy lịch sử bản ghi
    // CreatedBy nlhoang 03/06/2020
    public function getAuditing($id)
    {
        $item = DB::table('audits')
            ->join('admin_users', 'audits.user_id', '=', 'admin_users.id')
            ->where('auditable_id', '=', $id)
            ->where('auditable_type', '=', 'App\Model\Entities\Customer')
            ->orderBy('audits.created_at', 'ASC')
            ->get([
                'admin_users.username',
                'audits.event',
                'audits.old_values',
                'audits.new_values',
                'audits.created_at'
            ]);
        return $item;
    }

    // API trả danh sách khách hàng thuộc quản lý của user
    // Created by ptly 2020.06.22
    public function getDataListByUser($request, $userId)
    {
        $status = $request['status'];
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $sorts = $request['sort'];
        $table_name = $this->getTableName();

        $query = DB::table($table_name)
            ->join('customer_group_customer', $table_name . '.id', '=', 'customer_group_customer.customer_id')
            ->join('customer_group', 'customer_group.id', '=', 'customer_group_customer.customer_group_id')
            ->join('admin_users_customer_group', 'admin_users_customer_group.customer_group_id', '=', 'customer_group.id')
            ->where([
                [$table_name . '.del_flag', '=', '0'],
                [$table_name . '.active', '=', '1'],
                ['admin_users_customer_group.admin_user_id', '=', $userId]
            ]);
        $query = $query->distinct();
        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $query->whereNotIn($table_name . '.id', $ids);
        }


        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.customer_code', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.mobile_no', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.full_name', 'like', '%' . $textSearch . '%');
            });
        }

        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                $query->orderBy($sort['sortField'], $sort['sortType']);
            }
        }

        $count = $query->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $query->skip($offset)->take($pageSize);
        $items = $query->get([
            $table_name . '.*',
            $table_name . '.id as key'
        ]);

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = DB::table($table_name)->whereIn($table_name . '.id', $ids)
                ->get([
                    $table_name . '.*',
                    $table_name . '.id as key'
                ]);
            if ($items) {
                if ($itemSelected && 0 < sizeof($itemSelected)) {
                    foreach ($itemSelected as $obj) {
                        $items->prepend($obj);
                    }
                }
            } else {
                $items = $itemSelected;
            }
        }

        return [
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'items' => $items
        ];
    }
}
