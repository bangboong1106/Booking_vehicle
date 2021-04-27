<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories\Client;

use Exception;

use App\Model\Entities\VehicleGroup;
use \App\Repositories\VehicleGroupRepository;
use App\Validators\VehicleGroupValidator;
use Illuminate\Support\Facades\DB;

class VehicleGroupClientRepository extends VehicleGroupRepository
{


    // API trả kết quả mobile
    // CreatedBy nlhoang 20/05/2020
    public function getDataByID($id)
    {
        $table_name = $this->getTableName();
        $item = DB::table($table_name)
            ->leftJoin($table_name . ' as parent', 'parent.id', '=', $table_name . '.parent_id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
                'parent.name as name_of_parent_id'
            ])->first();
        return $item;
    }

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {
        $item = VehicleGroup::whereIn('id', $id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid location id: ' . $id);
        }
    }
}
