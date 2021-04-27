<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories\Management;

use App\Model\Entities\VehicleTeam;
use App\Repositories\VehicleTeamRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class VehicleTeamManagementRepository extends VehicleTeamRepository
{

    // API lấy thông tin nhóm xe
    // CreatedBy nlhoang 19/05/2020
    public function getManagementItemList($request)
    {
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $sorts = $request['sort'];

        $table_name = $this->getTableName();
        $query = DB::table($table_name)
            ->leftJoin('partner as p', $table_name . '.partner_id', '=', 'p.id')
            ->where([
                [$table_name . '.del_flag', '=', '0'],
            ]);

        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $query->whereNotIn($table_name . '.id', $ids);
        }

        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.code', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.name', 'like', '%' . $textSearch . '%');
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
            $table_name . '.id as key',
            'p.full_name as name_of_partner_id'
        ]);

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = DB::table($table_name)->whereIn($table_name . '.id', $ids)
                ->get([
                    $table_name . '.*',
                    $table_name . '.id as key',
                    'p.full_name as name_of_partner_id'
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
            ->leftJoin('drivers', $table_name . '.capital_driver_id', '=', 'drivers.id')
            ->leftJoin('partner as p', $table_name . '.partner_id', '=', 'p.id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
                'drivers.full_name as name_of_capital_driver_id',
                'p.full_name as name_of_partner_id',

            ])->first();
        $item->drivers = DB::table($table_name)
            ->leftJoin('driver_vehicle_team as dvh', 'dvh.vehicle_team_id', '=', 'vehicle_team.id')
            ->leftJoin('drivers', 'drivers.id', '=', 'dvh.driver_id')
            ->where($table_name . '.id', '=', $id)
            ->distinct()
            ->get([
                'drivers.id as driver_id',
                'drivers.full_name as name_of_driver_id'
            ]);
        return $item;
    }

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {
        $item = VehicleTeam::find($id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid location id: ' . $id);
        }
    }

    // API lưu đối tượng
    // CreatedBy nlhoang 27/05/2020
    public function preSaveEntity($entity, $parameters)
    {
        return $entity;
    }

    // API lưu đối tượng
    // CreatedBy nlhoang 27/05/2020
    public function saveRelationEntity($entity, $parameters)
    {
        $drivers = $parameters['drivers'];
        $driver_ids = array_diff(explode(";", $drivers), array(""));
        $entity->drivers()->sync($driver_ids);
    }
}
