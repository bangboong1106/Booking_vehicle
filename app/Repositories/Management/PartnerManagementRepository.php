<?php

namespace App\Repositories\Management;

use App\Model\Entities\GoodsUnit;
use App\Model\Entities\Partner;
use App\Repositories\PartnerRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class PartnerManagementRepository extends PartnerRepository
{

    // API lấy thông tin địa điểm
    // CreatedBy nlhoang 19/05/2020
    public function getManagementItemList($request)
    {
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $sorts = $request['sort'];

        $table_name = $this->getTableName();
        $query = DB::table($table_name)
            ->where([
                [$table_name . '.del_flag', '=', '0'],
            ]);

        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $query->whereNotIn($table_name . '.id', $ids);
        }

        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.full_name', 'like', '%' . $textSearch . '%')
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
        $item = Partner::find($id);
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
