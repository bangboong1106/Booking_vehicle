<?php

namespace App\Repositories\Client;

use App\Model\Entities\CustomerDefaultData;
use App\Repositories\CustomerDefaultDataRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class CustomerDefaultDataClientRepository extends CustomerDefaultDataRepository
{
    protected function getClientColumns($clientID, $customerID, $table_name)
    {
        $columns = [
            $table_name . '.*',
            $table_name . '.id as key',
            DB::raw('c.full_name as name_of_client_id'),
            DB::raw('ld.title as name_of_location_destination_id'),
            DB::raw('la.title as name_of_location_arrival_id'),
        ];
        return $columns;
    }

    protected function getClientBuilder($clientID, $customerID, $table_name, $columns, $request)
    {
        return DB::table($table_name)
            ->leftJoin('customer as c', $table_name . '.client_id', '=', 'c.id')
            ->leftJoin('locations as ld', $table_name . '.location_destination_id', '=', 'ld.id')
            ->leftJoin('locations as la', $table_name . '.location_arrival_id', '=', 'la.id')
            ->where($table_name . '.del_flag', '=', 0)
            ->select($columns);
    }

    // API lưu đối tượng
    // CreatedBy nlhoang 20/11/2020
    public function preSaveEntity($entity, $parameters)
    {
        $entity->location_arrival_ids = $parameters['location_arrival_id'];
        $entity->location_destination_ids =  $parameters['location_destination_id'];
        return $entity;
    }

    // API lấy giá trị mặc định của khách hàng
    // CreatedBy nlhoang 20/11/2020
    public function getDefaultDataByClientID($clientID)
    {
        $table_name = $this->getTableName();
        return DB::table($table_name)
            ->leftJoin('customer as c', $table_name . '.client_id', '=', 'c.id')
            ->leftJoin('locations as ld', $table_name . '.location_destination_id', '=', 'ld.id')
            ->leftJoin('locations as la', $table_name . '.location_arrival_id', '=', 'la.id')
            ->where($table_name . '.client_id', '=', $clientID)
            ->where($table_name . '.del_flag', '=', 0)
            ->orderBy($table_name . '.upd_date', 'desc')
            ->select([
                DB::raw('ld.id as location_destination_id'),
                DB::raw('la.id as location_arrival_id'),
                DB::raw('ld.title as name_of_location_destination_id'),
                DB::raw('la.title as name_of_location_arrival_id'),
            ])
            ->take(1)
            ->get()->first();
    }

    public function deleteDataByID($id)
    {
        $item = CustomerDefaultData::whereIn('id', $id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid id: ' . $id);
        }
    }
}
