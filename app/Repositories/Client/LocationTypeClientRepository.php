<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories\Client;

use App\Model\Entities\LocationType;
use App\Model\Entities\Province;
use App\Repositories\Base\CustomRepository;
use App\Repositories\LocationTypeRepository;
use DB;
use Exception;

class LocationTypeClientRepository extends LocationTypeRepository
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
        $item = LocationType::whereIn('id', $id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid id: ' . $id);
        }
    }
}
