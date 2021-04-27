<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories\Client;


use App\Model\Entities\District;
use App\Repositories\DistrictRepository;
use App\Validators\DistrictValidator;
use DB;

class DistrictClientRepository extends DistrictRepository
{

    protected function getIgnoreClientID()
    {
        return true;
    }

    protected function getClientColumns($clientID, $customerID, $table_name)
    {
        $columns = [
            $table_name . '.district_id as id',
            $table_name . '.title as title',
            $table_name . '.id as key',
            'm_province.title as name_of_province_id',
        ];
        return $columns;
    }

    protected function getClientBuilder($clientID, $customerID, $table_name, $columns, $request)
    {
        $query = DB::table($table_name)
            ->leftJoin('m_province', $table_name . '.province_id', '=', 'm_province.province_id')
            ->where([
                [$table_name . '.del_flag', '=', 0],
            ])
            ->select($columns);

        $province_id = isset($request['province_id']) ? $request['province_id'] : '';
        if (!empty($province_id)) {
            $query->where($table_name . '.province_id', $province_id);
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
            ->where($table_name . '.district_id', '=', $id)
            ->get([
                $table_name . '.*',
            ])->first();
        return $item;
    }
}
