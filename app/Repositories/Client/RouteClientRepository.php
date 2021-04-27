<?php

namespace App\Repositories\Client;

use App\Model\Entities\GoodsType;
use App\Repositories\RoutesRepository;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RouteClientRepository extends RoutesRepository
{
    protected function getClientBuilder($clientID, $customerID, $table_name, $columns, $request)
    {
        return DB::table($table_name)
            ->where([
                [$table_name . '.del_flag', '=', '0'],
            ])->select($columns);
    }

    // API trả kết quả mobile
    // CreatedBy nlhoang 20/05/2020
    public function getDataByID($id)
    {
        $table_name = $this->getTableName();
        $item = DB::table($table_name)
            ->leftJoin('drivers as d', $table_name . '.driver_id', '=', 'd.id')
            ->leftJoin('vehicle as v', $table_name . '.vehicle_id', '=', 'v.id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
            ])->first();
        return $item;
    }

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {
        $item = GoodsType::whereIn('id', $id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid entity id: ' . $id);
        }
    }

    // API lưu đối tượng
    // CreatedBy nlhoang 28/05/2020
    public function preSaveEntity($entity, $parameters)
    {
        return $entity;
    }

    // API lưu quan hệ
    // CreatedBy nlhoang 28/05/2020
    public function saveRelationEntity($entity, $parameters)
    {
    }
}
