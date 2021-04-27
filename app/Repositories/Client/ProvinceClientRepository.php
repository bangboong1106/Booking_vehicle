<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories\Client;


use App\Model\Entities\Province;
use App\Repositories\Base\CustomRepository;
use App\Repositories\ProvinceRepository;
use App\Validators\ProvinceValidator;
use DB;

class ProvinceClientRepository extends ProvinceRepository
{

    protected function getIgnoreClientID()
    {
        return true;
    }

    protected function getClientColumns($clientID, $customerID, $table_name)
    {
        $columns = [
            $table_name . '.*',
            $table_name . '.province_id as id',
            $table_name . '.province_id as key'
        ];
        return $columns;
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
            ->where($table_name . '.province_id', '=', $id)
            ->get([
                $table_name . '.*',
            ])->first();
        return $item;
    }
}
