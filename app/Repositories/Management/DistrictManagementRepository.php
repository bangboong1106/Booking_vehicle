<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories\Management;


use App\Model\Entities\District;
use App\Repositories\DistrictRepository;
use App\Validators\DistrictValidator;
use DB;

class DistrictManagementRepository extends DistrictRepository
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
        $province_id = $request['province_id'];


        $table_name = $this->getTableName();
        $query = DB::table($table_name)
            ->leftJoin('m_province', $table_name . '.province_id', '=', 'm_province.province_id')
            ->where([
                [$table_name . '.del_flag', '=', '0'],
            ]);

        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $query->whereNotIn($table_name . '.district_id', $ids);
        }

        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.title', 'like', '%' . $textSearch . '%');
            });
        }

        if (!empty($province_id)) {
            $query->where($table_name . '.province_id', $province_id);
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
        $items = $query->get([
            $table_name . '.district_id as id',
            $table_name . '.title as title',
            $table_name . '.id as key',
            'm_province.title as name_of_province_id',
        ]);

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = DB::table($table_name)->whereIn($table_name . '.id', $ids)
                ->get([
                    $table_name . '.district_id as id',
                    $table_name . '.title as title',
                    $table_name . '.id as key',
                    'm_province.title as name_of_province_id',
                ]);
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
            ->where($table_name . '.district_id', '=', $id)
            ->get([
                $table_name . '.*',
            ])->first();
        return $item;
    }
}