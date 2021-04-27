<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories\Management;

use App\Common\AppConstant;
use App\Model\Entities\AdminUserInfo;
use App\Model\Entities\CustomerGroup;
use App\Model\Entities\VehicleTeam;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\CustomerGroupRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class AdminUserManagementRepository extends AdminUserInfoRepository
{

    // API lấy danh sách nhóm khách hàng
    // CreatedBy ptly 2020.06.22
    public function getManagementItemList($request)
    {
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $sorts = $request['sort'];

        $table_name = $this->getTableName();
        $query = DB::table($table_name)
            ->where([
                [$table_name . '.del_flag', '=', '0'],
                [$table_name . '.role', '=', 'admin']
            ]);

        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $query->whereNotIn($table_name . '.id', $ids);
        }

        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.username', 'like', '%' . $textSearch . '%')
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
            '*',
            'id as key'
        ]);

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = DB::table($table_name)->whereIn($table_name . '.id', $ids)
                ->get([
                    '*',
                    'id as key',
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
            ->leftJoin('files', $table_name . '.avatar_id', '=', 'files.file_id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
                'files.path',
                'files.file_type',
            ])->first();
        $item->path_of_avatar_id = AppConstant::getImagePath($item->path, $item->file_type);

        $item->vehicleTeams = DB::table($table_name . ' as q')
            ->leftJoin('admin_users_vehicle_teams as rl', 'rl.admin_user_id', '=', 'q.id')
            ->join('vehicle_team', 'vehicle_team.id', '=', 'rl.vehicle_team_id')
            ->where('q.id', $id)
            ->where('vehicle_team.del_flag', '=', 0)
            ->get([
                'vehicle_team.id as vehicle_team_id',
                'vehicle_team.name as name_of_vehicle_team_id'
            ]);

        $item->customerGroups = DB::table($table_name . ' as q')
            ->leftJoin('admin_users_customer_group as rl', 'rl.admin_user_id', '=', 'q.id')
            ->join('customer_group', 'customer_group.id', '=', 'rl.customer_group_id')
            ->where('q.id', $id)
            ->where('customer_group.del_flag', '=', 0)
            ->get([
                'customer_group.id as customer_group_id',
                'customer_group.name as name_of_customer_group_id'
            ]);

        $item->roles = DB::table($table_name . ' as q')
            ->leftJoin('model_has_roles as rl', 'rl.model_id', '=', 'q.id')
            ->join('roles', 'roles.id', '=', 'rl.role_id')
            ->where('q.id', $id)
            ->where('roles.del_flag', '=', 0)
            ->get([
                'roles.id as role_id',
                'roles.title as name_of_role_id'
            ]);

        return $item;
    }

    // API xoá
    // CreatedBy ptly 26/06/2020
    public function deleteDataByID($id)
    {
        $item = AdminUserInfo::find($id);
        if (!is_null($item)) {
            $item->delete();

        } else {
            new Exception('Invalid admin user id: ' . $id);
        }
    }

    // API lưu đối tượng
    //  CreatedBy ptly 26/06/2020
    public function preSaveEntity($entity, $parameters)
    {
        return $entity;
    }

}
