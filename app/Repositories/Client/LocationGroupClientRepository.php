<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories\Client;

use App\Model\Entities\LocationGroup;
use App\Model\Entities\Province;
use App\Repositories\Base\CustomRepository;
use App\Repositories\LocationGroupRepository;
use App\Repositories\ProvinceRepository;
use App\Validators\ProvinceValidator;
use DB;
use Exception;

class LocationGroupClientRepository extends LocationGroupRepository
{
    protected function getWhereTextSearch($query, $table_name, $textSearch)
    {
        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.title', 'like', '%' . $textSearch . '%');
            });
        }
        return $query;
    }
    
    public function deleteDataByID($id)
    {
        $item = LocationGroup::whereIn('id', $id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid id: ' . $id);
        }
    }
}
