<?php

namespace App\Repositories\Client;

use App\Model\Entities\GoodsUnit;
use App\Repositories\GoodsUnitRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class GoodsUnitClientRepository extends GoodsUnitRepository
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
        $item = GoodsUnit::whereIn('id', $id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid id: ' . $id);
        }
    }
}
