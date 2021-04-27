<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories\Client;


use App\Model\Entities\Ward;
use App\Repositories\WardRepository;
use App\Validators\WardValidator;
use DB;

class WardClientRepository extends WardRepository
{
    protected function getIgnoreClientID()
    {
        return true;
    }

    protected function getClientColumns($clientID, $customerID, $table_name)
    {
        $columns = [
            $table_name . '.ward_id as id',
            $table_name . '.title as title',
            $table_name . '.id as key',
            'm_district.title as name_of_district_id',
            'm_province.title as name_of_province_id',
        ];
        return $columns;
    }

    protected function getClientBuilder($clientID, $customerID, $table_name, $columns, $request)
    {
        $query = DB::table($table_name)
            ->leftJoin('m_district', $table_name . '.district_id', '=', 'm_district.district_id')
            ->leftJoin('m_province', 'm_district.province_id', '=', 'm_province.province_id')
            ->where([
                [$table_name . '.del_flag', '=', 0],
            ])
            ->select($columns);

        $district_id = isset($request['district_id']) ? $request['district_id'] : '';
        if (!empty($district_id)) {
            $query->where($table_name . '.district_id', $district_id);
        }
        return $query;
    }

    protected function getWhereTextSearch($query, $table_name, $textSearch)
    {
        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.title', 'like', '%' . $textSearch . '%');
            });
        }
        return $query;
    }
    
    public function getDataByID($id)
    {
        $table_name = $this->getTableName();
        $item = DB::table($table_name)
            ->where($table_name . '.ward_id', '=', $id)
            ->get([
                $table_name . '.*',
            ])->first();
        return $item;
    }
}
