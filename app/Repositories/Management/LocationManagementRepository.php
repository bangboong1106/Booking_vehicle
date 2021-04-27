<?php

namespace App\Repositories\Management;

use App\Model\Entities\Location;
use App\Repositories\LocationRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class LocationManagementRepository extends LocationRepository
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
        $customer_id = $request['customer_id'];

        $table_name = $this->getTableName();
        $query = DB::table($table_name)
            ->leftJoin('customer as c', $table_name . '.customer_id', '=', 'c.id')
            ->leftJoin('admin_users as ai', 'ai.id', '=', $table_name . '.ins_id')
            ->leftJoin('admin_users as au', 'au.id', '=', $table_name . '.upd_id')

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
            'c.full_name as name_of_customer_id',
            'ai.username as name_of_ins_id',
            'au.username as name_of_upd_id',
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
            ->leftJoin('customer as c', $table_name . '.customer_id', '=', 'c.id')
            ->leftJoin('admin_users as ai', 'ai.id', '=', $table_name . '.ins_id')
            ->leftJoin('admin_users as au', 'au.id', '=', $table_name . '.upd_id')
            ->leftJoin('m_province', $table_name . '.province_id', '=', 'm_province.province_id')
            ->leftJoin('m_district', $table_name . '.district_id', '=', 'm_district.district_id')
            ->leftJoin('m_ward', $table_name . '.ward_id', '=', 'm_ward.ward_id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
                'm_province.title as name_of_province_id',
                'm_district.title as name_of_district_id',
                'm_ward.title as name_of_ward_id',
                'c.full_name as name_of_customer_id',
                'ai.username as name_of_ins_id',
                'au.username as name_of_upd_id',
            ])->first();
        return $item;
    }

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {
        $item = Location::find($id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid location id: ' . $id);
        }
    }

    // API lưu đối tượng
    // CreatedBy nlhoang 28/05/2020
    public function preSaveEntity($entity, $parameters)
    {
        $entity->address_auto_code = $entity->province_id . ' - ' . $entity->district_id . ' - ' . $entity->ward_id;
        return $entity;
    }

    // API lưu quan hệ
    // CreatedBy nlhoang 28/05/2020
    public function saveRelationEntity($entity, $parameters)
    {
    }
}
