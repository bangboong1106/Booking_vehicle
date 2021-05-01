<?php

namespace App\Repositories\Client;

use App\Model\Entities\GoodsType;
use App\Repositories\GoodsTypeRepository;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Common\AppConstant;

class GoodsTypeClientRepository extends GoodsTypeRepository
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
            DB::raw('goods_unit.title as name_of_goods_unit_id'),
            DB::raw('goods_group.name as name_of_goods_group'),
            DB::raw('files.path'),
            DB::raw('files.file_type'),
            DB::raw('goods_group.code as goods_group_code'),
            DB::raw('goods_group.name as goods_group_name'),
        ];
        if ($customerID) {
            $columns[] = DB::raw("IF(locations.ins_id = $customerID ,TRUE,FALSE) as is_allow_update");
        }
        return $columns;
    }

    protected function getClientBuilder($clientID, $customerID, $table_name, $columns, $request)
    {
        return DB::table($table_name)
            ->leftJoin('goods_unit', $table_name . '.goods_unit_id', '=', 'goods_unit.id')
            ->leftJoin('files', $table_name . '.file_id', '=', 'files.file_id')
            ->leftJoin('goods_group', $table_name . '.goods_group_id', '=', 'goods_group.id')
            ->where([
                [$table_name . '.del_flag', '=', '0']
            ])->select($columns);
    }

    protected function getWhereTextSearch($query, $table_name, $textSearch)
    {
        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.title', 'like', '%' . $textSearch . '%')
                ->orwhere($table_name . '.name_of_goods_group', 'like', '%' . $textSearch . '%');
            });
        }
        return $query;
    }

    protected function getClientItems($items)
    {
        if ($items instanceof Collection) {
            foreach ($items as $item) {
                $item->path_of_file_id = AppConstant::getImagePath($item->path, $item->file_type);
            }
        } else {
            $items->path_of_file_id = AppConstant::getImagePath($items->path, $items->file_type);
        }
        return $items;
    }

    public function getDataForClientByID($customerID, $id)
    {
        $item = parent::getDataForClientByID($customerID, $id);
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
