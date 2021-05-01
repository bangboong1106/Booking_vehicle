<?php

namespace App\Repositories\Client;

use App\Model\Entities\Location;
use App\Repositories\LocationRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class LocationClientRepository extends LocationRepository
{

    protected function getIgnoreClientID()
    {
        return true;
    }

    protected function getClientColumns($clientID, $customerID, $table_name)
    {
        $columns = [
            $table_name . '.*',
            $table_name . '.id as key',
            DB::raw('m_province.title as name_of_province_id'),
            DB::raw('m_district.title as name_of_district_id'),
            DB::raw('m_ward.title as name_of_ward_id'),
            DB::raw('lg.title as name_of_location_group_id'),
            DB::raw('lt.title as name_of_location_type_id'),
        ];

        if ($customerID) {
            $columns[] = DB::raw("IF(locations.ins_id = $customerID ,TRUE,FALSE) as is_allow_update");
        }

        return $columns;
    }

    protected function getClientBuilder($clientID, $customerID, $table_name, $columns, $request)
    {
        return DB::table($table_name)
            ->leftJoin('m_province', 'locations.province_id', '=', 'm_province.province_id')
            ->leftJoin('m_district', 'locations.district_id', '=', 'm_district.district_id')
            ->leftJoin('m_ward', 'locations.ward_id', '=', 'm_ward.ward_id')
            ->leftJoin('location_group as lg', 'lg.id', '=', 'locations.location_group_id')
            ->leftJoin('location_type as lt', 'lt.id', '=', 'locations.location_type_id')
            ->leftJoin('admin_users', 'locations.ins_id', '=', 'admin_users.id')
            ->where($table_name . '.del_flag', '=', 0)
            ->select($columns);
    }

    protected function getWhereTextSearch($query, $table_name, $textSearch)
    {
        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.title', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.code', 'like', '%' . $textSearch . '%');
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
            ->leftJoin('location_group as lg', 'lg.id', '=', $table_name . '.location_group_id')
            ->leftJoin('location_type as lt', 'lt.id', '=', $table_name . '.location_type_id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
                'm_province.title as name_of_province_id',
                'm_district.title as name_of_district_id',
                'm_ward.title as name_of_ward_id',
                'lg.title as name_of_location_group_id',
                'lt.title as name_of_location_type_id'
            ])->first();
        return $item;
    }

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {
        $item = Location::whereIn('id', $id);
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
        $entity->address_auto_code = $entity->province_id . ' - ' . $entity->district_id . ' - ' . $entity->ward_id;
        return $entity;
    }

    // API lưu quan hệ
    // CreatedBy nlhoang 28/05/2020
    public function saveRelationEntity($entity, $parameters)
    {
    }
}
