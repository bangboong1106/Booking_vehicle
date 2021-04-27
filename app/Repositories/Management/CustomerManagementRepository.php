<?php

namespace App\Repositories\Management;

use App\Common\AppConstant;
use App\Model\Entities\Customer;
use App\Model\Entities\AdminUserInfo;
use \App\Repositories\CustomerRepository;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CustomerManagementRepository extends CustomerRepository
{

    // API trả kết quả mobile
    // CreatedBy nlhoang 19/05/2020
    public function getManagementItemList($request)
    {
        $status = $request['status'];
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $sorts = $request['sort'];
        $isDeleted = '0';
        if (!empty($request['isDeleted'])) {
            $isDeleted = $request['isDeleted'] == "true" ? '1' : '0';
        }

        $table_name = $this->getTableName();

        $query = DB::table($table_name)
            ->leftJoin('admin_users', 'admin_users.id', '=', $table_name . '.upd_id')
            ->where([
                [$table_name . '.del_flag', '=', $isDeleted],
                [$table_name . '.active', '=', '1'],
                [$table_name . '.customer_type', '=', '1']

            ]);

        logInfo($query->toSql());
        
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
            'admin_users.username as name_of_upd_id',
            $table_name . '.id as key'
        ]);

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = DB::table($table_name)->whereIn($table_name . '.id', $ids)
                ->get([
                    '*',
                    'id as key'
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
        $item = Customer::find($id);
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
    public function saveRelationEntity($entity, $parameters)
    {
        if (empty($entity->user_id)) {
            $user = $entity->adminUser()->create([
                'username' => $parameters['username'],
                'password' => empty($parameters['password']) ? '' : genPassword($parameters['password']),
                'full_name' => $entity->full_name,
                'email' => $parameters['email'],
                'role' => 'customer'
            ]);
            $entity->user_id = $user->id;
            $entity->save();
        } else {
            $user = AdminUserInfo::where('id', $entity->user_id)->first();
            $user->update([
                'username' => $parameters['username'],
                'password' => empty($parameters['password']) ? $user->password : genPassword($parameters['password']),
                'full_name' => $entity->full_name,
                'email' => $parameters['email'],
            ]);
        }
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
            ->leftJoin('customer_group_customer as cgc', function ($join) {
                $join->on('cgc.customer_id', '=', 'customer.id')
                    ->where('cgc.del_flag', '=', 0);
            })->leftJoin('admin_users_customer_group as aucg', function ($join) {
                $join->on('aucg.customer_group_id', '=', 'cgc.customer_group_id')
                    ->where('aucg.del_flag', '=', 0);
            })
            ->where([
                [$table_name . '.del_flag', '=', '0'],
                [$table_name . '.active', '=', '1'],
                [$table_name . '.customer_type', '=', '1']
            ]);
        $query = $query->where(function ($query) use ($userId) {
            $query->where('aucg.admin_user_id', '=', $userId)
                ->orWhereNull('aucg.customer_group_id');
        });
        $query = $query->distinct($table_name . '.id');
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

        $count = $query->count($table_name . '.id');
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
