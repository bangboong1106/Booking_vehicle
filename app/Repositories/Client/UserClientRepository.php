<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories\Client;


use App\Repositories\AdminUserInfoRepository;
use DB;

class UserClientRepository extends AdminUserInfoRepository
{

    // Hàm lấy thông tin người dùng đang đăng nhập
    // CreatedBy nlhoang 02/06/2020
    public function getDataByID($id)
    {
        $table_name = $this->getTableName();
        $item = DB::table($table_name)
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
            ])->first();

        $item->roles =  DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_id', '=', $id)
            ->get([
                'model_has_roles.role_id',
                'roles.name as name_of_role_id',
            ]);
        $item->vehicleTeams = DB::table('admin_users_vehicle_teams')
            ->join('vehicle_team', 'vehicle_team.id', '=', 'admin_users_vehicle_teams.vehicle_team_id')
            ->where('admin_users_vehicle_teams.admin_user_id', '=', $id)
            ->get([
                'admin_users_vehicle_teams.*',
                'vehicle_team.name as name_of_vehicle_team_id',
            ]);

        return $item;
    }

    // Hàm lấy danh sách quyền của người dùng
    // CreatedBy nlhoang 17/06/2020
    public function getPermissionsByUserId($id)
    {

        $permissions =  DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->join('role_has_permissions', 'role_has_permissions.role_id', '=', 'roles.id')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->where('model_has_roles.model_id', '=', $id)
            ->where('permissions.del_flag', '=', 0)
            ->distinct()
            ->orderBy('permissions.name')
            ->get([
                'permissions.name',
            ])->pluck('name');

        return $permissions;
    }
}
