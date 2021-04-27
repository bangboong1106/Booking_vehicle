<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories\Client;

use App\Model\Entities\AdminUserInfo;
use App\Model\Entities\CustomerGroup;
use App\Model\Entities\VehicleTeam;
use App\Repositories\AdminUserInfoRepository;
use App\Repositories\CustomerGroupRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class AdminUserClientRepository extends AdminUserInfoRepository
{

    // API lấy danh sách nhóm khách hàng
    // CreatedBy ptly 2020.06.22
    public function getDataList($request)
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
                $query->where($table_name . '.username', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.full_name', 'like', '%' . $textSearch . '%');
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
        $items = $query->get([
            '*',
            'id as key'
        ]);

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = DB::table($table_name)->whereIn($table_name . '.id', $ids)
                ->get([
                    '*',
                    'id as key',
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
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*'
            ])->first();
        return $item;
    }

    // API xoá
    // CreatedBy ptly 26/06/2020
    public function deleteDataByID($id)
    {
        $item = AdminUserInfo::whereIn('id', $id);
        if (!is_null($item)) {
            $item->delete();

        } else {
            new Exception('Invalid admin user id: ' . $id);
        }
    }

    // API lưu đối tượng
    //  CreatedBy ptly 26/06/2020
    public function preSaveEntity($entity, $parameters)
    {
        return $entity;
    }

}
