<?php

namespace App\Repositories\Management;

use App\Model\Entities\RepairTicket;
use App\Repositories\RepairTicketRepository;
use Exception;
use Illuminate\Support\Facades\DB;

class RepairTicketManagementRepository extends RepairTicketRepository
{

    // API lấy danh sách phiếu sửa chữa
    // CreatedBy ptly 27/08/2020
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
            $query->where($table_name . '.name', 'like', '%' . $textSearch . '%');
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

    // API lấy thông tin chi tiết phiếu sửa chữa
    // CreatedBy ptly 27/08/2020
    public function getDataByID($id)
    {
        $table_name = $this->getTableName();
        $item = DB::table($table_name)
            ->where($table_name . '.id', '=', $id)
            ->leftJoin('drivers', 'drivers.id', '=', $table_name . '.driver_id')
            ->leftJoin('vehicle', 'vehicle.id', '=', $table_name . '.vehicle_id')
            ->get([
                $table_name . '.*',
                'drivers.full_name as name_of_driver_id',
                'vehicle.reg_no as name_of_vehicle_id',
            ])->first();
        $item->accessories = DB::table('repair_ticket_item')
            ->join('accessory', 'repair_ticket_item.accessory_id', '=', 'accessory.id')
            ->where('repair_ticket_item.repair_ticket_id', '=', $id)
            ->get([
                'repair_ticket_item.*',
                'accessory.name as name_of_accessory_id',
            ]);
        return $item;
    }
    // API xoá
    // CreatedBy ptly 27/08/2020
    public function deleteDataByID($id)
    {
        $item = RepairTicket::find($id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid entity id: ' . $id);
        }
    }

    // API lưu đối tượng
    // CreatedBy ptly 27/08/2020
    public function preSaveEntity($entity, $parameters)
    {
        return $entity;
    }

    /**
     * API lưu quan hệ
     * @param $entity
     * @param $parameters RepairTicket
     * CreatedBy ptly 27/08/2020
     */
    public function saveRelationEntity($entity, $parameters)
    {
        //Lưu các phụ tùng sửa chữa vào bảng repair_ticket_item
        $items = $parameters['accessories'];
        if (empty($items)) return;
        $data = [];
        $total_amount = 0;
        foreach ($items as $item) {
            $data[] = [
                'accessory_id' => isset($item['accessory_id']) ? $item['accessory_id'] : null,
                'quantity' => isset($item['quantity']) ? $item['quantity'] : 0,
                'price' => isset($item['price']) ? $item['price'] : 0,
                'amount' => isset($item['amount']) ? $item['amount'] : 0,
                'next_repair_date' => isset($item['next_repair_date']) ? $item['next_repair_date'] : null,
                'next_repair_distance' => isset($item['next_repair_distance']) ? $item['next_repair_distance'] : null,
            ];
            $total_amount += isset($item['amount']) ? $item['amount'] : 0;
        }
        $entity->repairTicketItems()->detach();
        $entity->repairTicketItems()->sync($data);
        $entity->amount = $total_amount; // Tính tổng tiền phiếu sửa chữa.
        $entity->save();
    }
}
