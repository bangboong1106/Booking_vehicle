<?php

namespace App\Repositories\Driver;

use App\Common\AppConstant;
use App\Model\Entities\Order;
use App\Model\Entities\RouteFile;
use App\Model\Entities\Routes;
use App\Repositories\GoodsUnitRepository;
use App\Repositories\OrderRepository;
use App\Validators\RoutesValidator;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class GoodsUnitDriverRepository extends GoodsUnitRepository
{
    // API trả kết quả mobile
    // CreatedBy nlhoang 26/10/2020
    public function getDataList($request)
    {
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $sorts = $request['sort'];
        $isDeleted = '0';
        if (!empty($request['isDeleted'])) {
            $isDeleted = $request['isDeleted'] == "true" ? '1' : '0';
        }

        $table_name = $this->getTableName();

        $query = DB::table($table_name)
            ->where([
                [$table_name . '.del_flag', '=', $isDeleted],
            ]);

        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $query->whereNotIn($table_name . '.id', $ids);
        }


        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.title', 'like', '%' . $textSearch . '%');
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
            $table_name . '.*',
            $table_name . '.id as key'
        ]);

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = DB::table($table_name)->whereIn($table_name . '.id', $ids)
                ->get([
                    '*',
                    'id as key'
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
}
