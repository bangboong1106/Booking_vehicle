<?php

namespace App\Repositories\Management;

use App\Model\Entities\GoodsType;
use App\Repositories\GoodsTypeRepository;
use App\Validators\GoodTypeValidator;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GoodsTypeManagementRepository extends GoodsTypeRepository
{
    // API lấy thông tin hàng hoá
    // CreatedBy nlhoang 19/05/2020
    public function getManagementItemList($request)
    {
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $sorts = $request['sort'];
        $table_name = $this->getTableName();
        $customer_id = $request['customer_id'];

        $query = DB::table($table_name)
            ->leftJoin('customer as c', $table_name . '.customer_id', '=', 'c.id')
            ->where([
                [$table_name . '.del_flag', '=', '0'],
            ]);
        if (!empty($customer_id)) {
            $query->where([
                [$table_name . '.customer_id', '=', $customer_id],
            ]);
        }
        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $query->whereNotIn($table_name . '.id', $ids);
        }

        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.title', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.code', 'like', '%' . $textSearch . '%');
            });
        }

        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                $query->orderBy($sort['sortField'], $sort['sortType']);
            }
        }

        $count = $query->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $query->skip($offset)->take($pageSize);
        $columns = [
            $table_name . '.*',
            $table_name . '.id as key',
            'c.full_name as name_of_customer_id'
        ];
        $items = $query->get($columns);

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = DB::table($table_name)->whereIn($table_name . '.id', $ids)
                ->get($columns);
            if ($items) {
                if ($itemSelected && 0 < sizeof($itemSelected)) {
                    foreach ($itemSelected as $obj) {
                        $items->prepend($obj);
                    }
                }
            } else {
                $items = $itemSelected;
            }
        }
        return [
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'items' => $items
        ];
    }

    // API trả kết quả mobile
    // CreatedBy nlhoang 20/05/2020
    public function getDataByID($id)
    {
        $table_name = $this->getTableName();
        $item = DB::table($table_name)
            ->leftJoin('goods_unit', $table_name . '.goods_unit_id', '=', 'goods_unit.id')
            ->leftJoin('customer as c', $table_name . '.customer_id', '=', 'c.id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
                'goods_unit.title as name_of_goods_unit_id',
                'c.full_name as name_of_customer_id'
            ])->first();
        return $item;
    }

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {
        $item = GoodsType::find($id);
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
