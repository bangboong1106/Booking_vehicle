<?php

namespace App\Repositories\Client;

use App\Common\AppConstant;
use App\Model\Entities\Vehicle;
use App\Repositories\VehicleRepository;
use App\Validators\VehicleValidator;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use stdClass;
use Exception;

class VehicleClientRepository extends VehicleRepository
{

    // API trả kết quả mobile
    // CreatedBy nlhoang 20/05/2020
    public function getDataByID($id)
    {
        $table_name = $this->getTableName();
        $item = DB::table($table_name)
            ->leftJoin('m_vehicle_group', $table_name . '.group_id', '=', 'm_vehicle_group.id')
            ->leftJoin('gps_company', $table_name . '.gps_company_id', '=', 'gps_company.id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
                'm_vehicle_group.name as name_of_group_id',
                'gps_company.name as name_of_gps_company_id',
            ])->first();

        $item->drivers = DB::table($table_name)
            ->leftJoin('driver_vehicle as dv', 'dv.vehicle_id', '=', $table_name . '.id')
            ->leftJoin('drivers', 'drivers.id', '=', 'dv.driver_id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                'drivers.id as driver_id',
                'drivers.full_name as name_of_driver_id'
            ]);


        $item->files = DB::table($table_name)
            ->join('vehicle_file as df', 'df.vehicle_id', '=', $table_name . '.id')
            ->join('vehicle_config_file as dv', 'dv.id', '=', 'df.vehicle_config_file_id')
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
        $item = Vehicle::whereIn('id', $id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid location id: ' . $id);
        }
    }

    /** API lấy thông tin để hiển thị lên bản đồ
     * CreatedBy nlhoang 25/05/2020
     */
    public function getVehiclesByUser($userId)
    {
        return Vehicle::query()
            ->select(
                "vehicle.id",
                "vehicle.reg_no as title",
                "vehicle.current_location",
                "vehicle.volume",
                "vehicle.weight",
                "vehicle.length",
                "vehicle.width",
                "vehicle.height",
                'vehicle.latitude',
                'vehicle.longitude',
                'vehicle.vehicle_plate',
                'vehicle.status'
            )
            ->join('driver_vehicle', 'vehicle.id', '=', 'driver_vehicle.vehicle_id')
            ->join('driver_vehicle_team', 'driver_vehicle.driver_id', '=', 'driver_vehicle_team.driver_id')
            ->join('admin_users_vehicle_teams', 'driver_vehicle_team.vehicle_team_id', '=', 'admin_users_vehicle_teams.vehicle_team_id')
            ->where('admin_users_vehicle_teams.admin_user_id', '=', $userId)
            ->groupBy('vehicle.id')
            ->orderBy('reg_no', 'asc')
            ->get();
    }

    // API lưu đối tượng
    // CreatedBy nlhoang 28/05/2020
    public function preSaveEntity($entity, $parameters)
    {
        $entity->vehicle_plate = str_replace(array("-", " ", "."), "", $entity->reg_no);
        return $entity;
    }

    // API lưu quan hệ
    // CreatedBy nlhoang 28/05/2020
    public function saveRelationEntity($entity, $parameters)
    {
        $drivers = $parameters['drivers'];
        $driver_ids = array_diff(explode(";", $drivers), array(""));
        $entity->drivers()->sync($driver_ids);
    }

    // API lấy lịch sử bản ghi
    // CreatedBy nlhoang 03/06/2020
    public function getAuditing($id)
    {
        $item = DB::table('audits')
            ->join('admin_users', 'audits.user_id', '=', 'admin_users.id')
            ->where('auditable_id', '=', $id)
            ->where('auditable_type', '=', 'App\Model\Entities\Vehicle')
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

    // Lấy danh sách xe thuộc quyền quản lý của user
    //Created by ptly 2020.06.22
    public function getDataListByUser($request, $userId)
    {
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $sorts = $request['sort'];

        $table_name = $this->getTableName();
        $query = DB::table($table_name)
            ->join('driver_vehicle', $table_name . '.id', '=', 'driver_vehicle.vehicle_id')
            ->join('driver_vehicle_team', 'driver_vehicle.driver_id', '=', 'driver_vehicle_team.driver_id')
            ->join('admin_users_vehicle_teams', 'driver_vehicle_team.vehicle_team_id', '=', 'admin_users_vehicle_teams.vehicle_team_id')
            ->where(
                [['admin_users_vehicle_teams.admin_user_id', '=', $userId],
                    [$table_name . '.del_flag', '=', '0']]
            );
        $query = $query->distinct();

        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $query->whereNotIn($table_name . '.id', $ids);
        }

        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.reg_no', 'like', '%' . $textSearch . '%');
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
                    $table_name . '.id as key',
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
