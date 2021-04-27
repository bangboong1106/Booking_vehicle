<?php

namespace App\Repositories\Management;

use App\Common\AppConstant;
use App\Model\Entities\Driver;
use App\Model\Entities\AdminUserInfo;
use App\Repositories\DriverRepository;
use DB;
use Exception;

class DriverManagementRepository extends DriverRepository
{

    // API trả danh sách cho app mobile
    // CreatedBy nlhoang 19/05/2020
    public function getManagementItemList($request)
    {
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $sorts = $request['sort'];
        $partner_id = $request['partner_id'];

        $isDeleted = '0';
        if (!empty($request['isDeleted'])) {
            $isDeleted = $request['isDeleted'] == "true" ? '1' : '0';
        }

        $table_name = $this->getTableName();
        $query = DB::table($table_name)
            ->leftJoin('partner as p', $table_name . '.partner_id', '=', 'p.id')
            ->leftJoin('admin_users', 'admin_users.id', '=', $table_name . '.upd_id')
            ->where([
                [$table_name . '.del_flag', '=', $isDeleted],
                [$table_name . '.active', '=', '1']
            ]);
        if (!empty($partner_id)) {
            $query->where($table_name . '.partner_id', '=', $partner_id);
        }
        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $query->whereNotIn($table_name . '.id', $ids);
        }

        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.code', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.mobile_no', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.full_name', 'like', '%' . $textSearch . '%');
            });
        }

        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                $query->orderBy($sort['sortField'], $sort['sortType']);
            }
        } else {
            $query->orderBy($table_name . '.full_name', 'ASC');
        }

        $count = $query->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $query->skip($offset)->take($pageSize);
        $items = $query->get(
            [
                'drivers.id as key',
                'drivers.*',
                'admin_users.username as name_of_upd_id',
                'p.full_name as name_of_partner_id',
            ]
        );

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = DB::table($table_name)->whereIn($table_name . '.id', $ids)
                ->get([
                    'drivers.id as key',
                    'drivers.*',
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

        $result = [
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'items' => $items
        ];
        return $result;
    }

    // API trả kết quả mobile
    // CreatedBy nlhoang 20/05/2020
    public function getDataByID($id)
    {
        $table_name = $this->getTableName();
        $item = DB::table($table_name)
            ->leftJoin('partner as p', $table_name . '.partner_id', '=', 'p.id')
            ->leftJoin('driver_vehicle_team', $table_name . '.id', '=', 'driver_vehicle_team.driver_id')
            ->leftJoin('vehicle_team', 'driver_vehicle_team.vehicle_team_id', '=', 'vehicle_team.id')
            ->leftJoin('files', $table_name . '.avatar_id', '=', 'files.file_id')
            ->leftJoin('admin_users', 'admin_users.id', '=', $table_name . '.user_id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
                'files.file_name as name_of_avatar_id',
                'files.file_type',
                'files.path',
                'admin_users.username',
                'admin_users.email',
                DB::raw('GROUP_CONCAT(vehicle_team.name) as name_of_vehicle_team_id'),
                DB::raw('GROUP_CONCAT(vehicle_team.id) as vehicle_team_id'),
                'p.full_name as name_of_partner_id',

            ])->first();
        $item->path_of_avatar_id = AppConstant::getImagePath($item->path, $item->file_type);


        $item->files = DB::table($table_name)
            ->join('driver_file as df', 'df.driver_id', '=', $table_name . '.id')
            ->join('driver_config_file as dv', 'dv.id', '=', 'df.driver_config_file_id')
            ->join('files', 'files.file_id', '=', 'df.file_id')
            ->where($table_name . '.id', '=', $id)
            ->where('df.del_flag', 0)
            ->get([
                'dv.*',
                'files.file_name as original_file_name',
                'files.file_type',
                'files.path',

            ]);

        foreach ($item->files as $file) {
            $file->file_path = AppConstant::getImagePath($file->path, $file->file_type);
        }
        return $item;
    }


    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {
        $item = Driver::find($id);
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
        $entity->full_name_accent = $entity->full_name;
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
                'role' => 'driver'
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
            ->where('auditable_type', '=', 'App\Model\Entities\Driver')
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

    // API trả danh sách tài xế thuộc quyền quản lý
    // CreatedBy nlhoang 19/05/2020
    public function getDataListByUser($request, $userId)
    {
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $sorts = $request['sort'];
        $table_name = $this->getTableName();

        $query = DB::table($table_name)
            ->join('driver_vehicle_team', $table_name . '.id', '=', 'driver_vehicle_team.driver_id')
            ->join('admin_users_vehicle_teams', 'driver_vehicle_team.vehicle_team_id', '=', 'admin_users_vehicle_teams.vehicle_team_id')
            ->where([
                [$table_name . '.del_flag', '=', '0'],
                [$table_name . '.active', '=', '1'],
                ['admin_users_vehicle_teams.admin_user_id', '=', $userId]
            ]);
        $query = $query->distinct($table_name . '.id');

        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $query->whereNotIn($table_name . '.id', $ids);
        }

        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.code', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.mobile_no', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.full_name', 'like', '%' . $textSearch . '%');
            });
        }

        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                $query->orderBy($sort['sortField'], $sort['sortType']);
            }
        } else {
            $query->orderBy($table_name . '.full_name', 'ASC');
        }

        $count = $query->count($table_name . '.id');
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $query->skip($offset)->take($pageSize);
        //        $query->groupBy(['vehicle_team.name', $table_name . '.code']);
        $items = $query->get(
            [
                'drivers.id as key',
                'drivers.*',
                //                'vehicle_team.name as vehicle_team_name', 'vehicle.reg_no as reg_no'
            ]
        );

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = DB::table($table_name)->whereIn($table_name . '.id', $ids)
                ->get([
                    'drivers.id as key',
                    'drivers.*',
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

        $result = [
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'items' => $items
        ];
        return $result;
    }
}
