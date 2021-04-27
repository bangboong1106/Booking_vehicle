<?php

namespace App\Repositories\Management;

use App\Model\Entities\Quota;
use App\Repositories\QuotaRepository;
use App\Validators\QuotaValidator;
use DB;
use Exception;
use Illuminate\Support\Str;

class QuotaManagementRepository extends QuotaRepository
{
    // API lấy thông tin bảng định mức
    // CreatedBy nlhoang 19/05/2020
    public function getManagementItemList($request)
    {
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $sorts = $request['sort'];

        $queryCount = DB::table('quota')
            ->where([
                ['quota.del_flag', '=', '0'],
            ]);

        $customersQuery = DB::table('quota')
            ->where([
                ['quota.del_flag', '=', '0'],
            ]);

        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids)) {
            $queryCount->whereNotIn('quota.id', 'in', $ids);
            $customersQuery->whereNotIn('quota.id', 'in', $ids);
        }

        if (!empty($textSearch)) {
            $queryCount->where(function ($query) use ($textSearch) {
                $query->where('quota.name', 'like', '%' . $textSearch . '%')
                    ->orWhere('quota.quota_code', 'like', '%' . $textSearch . '%');
            });
            $customersQuery->where(function ($query) use ($textSearch) {
                $query->where('quota.name', 'like', '%' . $textSearch . '%')
                    ->orWhere('quota.quota_code', 'like', '%' . $textSearch . '%');
            });
        }

        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                $customersQuery->orderBy($sort['sortField'], $sort['sortType']);
            }
        }

        $count = $queryCount->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $customersQuery->skip($offset)->take($pageSize);
        $customers = $customersQuery->get([
            '*',
            'id as key'
        ]);

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = DB::table('quota')->whereIn('quota.id', $ids)
                ->get([
                    '*',
                    'id as key'
                ]);
            if ($customers) {
                if ($itemSelected && 0 < sizeof($itemSelected)) {
                    foreach ($itemSelected as $obj) {
                        $customers->prepend($obj);
                    }
                }
            } else {
                $customers = $itemSelected;
            }
        }
        return [
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'items' => $customers
        ];
    }

    // API trả kết quả mobile
    // CreatedBy nlhoang 20/05/2020
    public function getDataByID($id)
    {
        $table_name = $this->getTableName();
        $item = DB::table($table_name)
            ->leftJoin('m_vehicle_group', $table_name . '.vehicle_group_id', '=', 'm_vehicle_group.id')
            ->leftJoin('locations as destination_location', $table_name . '.location_destination_id', '=', 'destination_location.id')
            ->leftJoin('locations as arrival_location', $table_name . '.location_arrival_id', '=', 'arrival_location.id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
                'm_vehicle_group.name as name_of_vehicle_group_id',
                'destination_location.title as name_of_location_destination_id',
                'arrival_location.title as name_of_location_arrival_id',
            ])->first();

        $item->locations = \DB::table($table_name . ' as q')
            ->leftJoin('quota_location as rl', 'rl.quota_id', '=', 'q.id')
            ->where('q.id', $id)
            ->where('rl.del_flag', '=', 0)
            ->orderBy('rl.location_order')
            ->get([
                'rl.location_id',
                'rl.location_title as name_of_location_id',
            ]);

        $item->costs = \DB::table($table_name . ' as q')
            ->leftJoin('quota_cost as rl', 'rl.quota_id', '=', 'q.id')
            ->leftJoin('m_receipt_payment as mrp', 'mrp.id', '=', 'rl.receipt_payment_id')
            ->where('q.id', $id)
            ->where('rl.del_flag', '=', 0)
            ->orderBy('rl.ins_date')
            ->get([
                'rl.receipt_payment_id',
                'mrp.name as name_of_receipt_payment_id',
                'rl.amount'
            ]);
        return $item;
    }

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {
        $item = Quota::find($id);
        if (!is_null($item)) {
            $item->delete();

        } else {
            new Exception('Invalid location id: ' . $id);
        }
    }
}
