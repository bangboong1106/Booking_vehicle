<?php

namespace App\Repositories\Management;

use App\Model\Entities\Order;
use App\Model\Entities\OrderCustomer;
use App\Model\Entities\OrderCustomerGoods;
use App\Model\Entities\OrderCustomerHistory;
use \App\Repositories\OrderCustomerRepository;
use App\Validators\OrderCustomerValidator;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;

class OrderCustomerManagementRepository extends OrderCustomerRepository
{

    // API lấy thông tin nhóm xe
    // CreatedBy nlhoang 19/05/2020
    public function getManagementItemList($request)
    {
        $status = $request['status'];
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $sorts = $request['sort'];
        $customerIds = $request['customers'];
        $fromDate = $request['fromDate'];
        $toDate = $request['toDate'];
        $userId = $request["userId"];

        $isDeleted = '0';
        if (!empty($request['isDeleted'])) {
            $isDeleted = $request['isDeleted'] == "true" ? '1' : '0';
        }

        $table_name = $this->getTableName();

        $query = DB::table($table_name)
            ->leftJoin('locations as ld', 'ld.id', '=', $table_name . '.location_destination_id')
            ->leftJoin('locations as la', 'la.id', '=', $table_name . '.location_arrival_id')
            ->leftJoin('customer as c', 'c.id', '=', $table_name . '.customer_id')
            ->leftJoin('customer_group_customer as cgc', function ($join) use ($table_name) {
                $join->on('cgc.customer_id', '=', $table_name . '.customer_id')
                    ->where('cgc.del_flag', '=', 0);
            })
            ->leftJoin('admin_users_customer_group as aucg', function ($join) {
                $join->on('aucg.customer_group_id', '=', 'cgc.customer_group_id')
                    ->where('aucg.del_flag', '=', 0);
            })
            ->leftJoin('admin_users', 'admin_users.id', '=', $table_name . '.upd_id')
            ->where([
                [$table_name . '.del_flag', '=', $isDeleted],
            ])
            ->whereIn($table_name . '.status', [5, 6, 7, 8])
            ->where(function ($query) use ($userId) {
                $query->where('aucg.admin_user_id', '=', $userId)
                    ->orWhereNull('aucg.customer_group_id');
            });

        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids)) {
            $query->whereNotIn($table_name . '.id', $ids);
        }
        if (isset($status) && 0 < sizeof($status)) {
            $query->whereIn($table_name . '.status', $status);
        }
        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.code', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.name', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.order_no', 'like', '%' . $textSearch . '%');
            });
        }
        if (!empty($fromDate)) {
            $query->whereDate($table_name . '.ETD_date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate($table_name . '.ETA_date', '<=', $toDate);
        }

        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                $query->orderBy($sort['sortField'], $sort['sortType']);
            }
        }
        if (!empty($customerIds)) {
            $query->whereIn($table_name . '.customer_id', $customerIds);
        }
        $count = $query->groupBy($table_name . '.id')->get()->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $query->groupBy($table_name . '.id')->skip($offset)->take($pageSize);
        $columns = [
            $table_name . '.*',
            'admin_users.username as name_of_upd_id',
            'c.full_name as name_of_customer_id',
            'ld.title as name_of_location_destination_id',
            'la.title as name_of_location_arrival_id',
            $table_name . '.id as key'
        ];
        $customers = $query->get($columns);

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = DB::table('order_customer')->whereIn($table_name . '.id', $ids)
                ->get($columns);
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
            ->leftJoin(
                DB::raw('(SELECT locations.id ,locations.latitude, locations.longitude, locations.full_address, locations.title FROM locations) as location_destination'),
                'location_destination.id',
                '=',
                $table_name . '.location_destination_id'
            )
            ->leftJoin(
                DB::raw('(SELECT locations.id ,locations.latitude, locations.longitude, locations.full_address, locations.title FROM locations) as location_arrival'),
                'location_arrival.id',
                '=',
                $table_name . '.location_arrival_id'
            )
            ->leftJoin('admin_users as i', 'i.id', '=', $table_name . '.ins_id')
            ->leftJoin('admin_users as u', 'u.id', '=', $table_name . '.upd_id')
            ->leftJoin('customer', 'customer.id', '=', $table_name . '.customer_id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
                'location_destination.title as name_of_location_destination_id',
                'location_arrival.title as name_of_location_arrival_id',
                'customer.customer_code  as name_of_customer_id',
                'i.username as name_of_ins_id',
                'u.username as name_of_upd_id'
            ])->first();

        $item->listVehicleGroup = DB::table($table_name)
            ->leftJoin('order_customer_vehicle_group as dvh', 'dvh.order_customer_id', '=', $table_name . '.id')
            ->leftJoin('m_vehicle_group', 'm_vehicle_group.id', '=', 'dvh.vehicle_group_id')
            ->where($table_name . '.id', '=', $id)
            ->where('dvh.del_flag', '=', 0)
            ->select([
                'dvh.vehicle_group_id',
                'dvh.vehicle_number',
                'm_vehicle_group.name as name_of_vehicle_group_id',

            ])
            ->get();

        $item->list_goods = DB::table($table_name)
            ->leftJoin('order_customer_goods as dvh', 'dvh.order_customer_id', '=', $table_name . '.id')
            ->leftJoin('goods_type as gt', 'gt.id', '=', 'dvh.goods_type_id')
            ->leftJoin('goods_unit as gu', 'gt.id', '=', 'dvh.goods_unit_id')
            ->where($table_name . '.id', '=', $id)
            ->where('dvh.del_flag', '=', 0)
            ->select([
                'dvh.*',
                'gt.title as name_of_goods_type_id',
                'gu.title as name_of_goods_unit_id',
            ])
            ->get();

        $item->orders = DB::table($table_name)
            ->leftJoin('orders as dvh', 'dvh.order_customer_id', '=', $table_name . '.id')
            ->where([
                [$table_name . '.id', '=', $id],
                ['dvh.del_flag', '=', '0']
            ])
            ->get([
                'dvh.*',
                'dvh.order_code as name_of_order_id'
            ]);
        return $item;
    }

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {
        $item = OrderCustomer::find($id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid location id: ' . $id);
        }
    }

    public function preSaveEntity($entity, $parameters)
    {
        if (!isset($parameters['id']) || empty($parameters['id'])) {
            $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order_customer'), null, true);
            $entity->code = $entity->order_no = $code;
            $this->_beforeStatus = 5;
        } else {
            $this->_beforeStatus = OrderCustomer::find($parameters['id'])->status;
        }
        $entity->status = 5; // Chủ hàng xác nhận
        if (!empty($parameters['list_goods'])) {
            $weight = array_reduce($parameters['list_goods'], function ($carry, $item) {
                return $carry + $item['total_weight'];
            });
            $entity->weight = $weight;
            $volume = array_reduce($parameters['list_goods'], function ($carry, $item) {
                return $carry + $item['total_volume'];
            });
            $entity->volume = $volume;
        }
        $entity->source_creation = config('constant.SOURCE_CREATE_CHU_HANG_ORDER_FORM');
        return $entity;
    }

    public function saveRelationEntity($entity, $parameters)
    {
        if (!empty($entity->id)) {
            OrderCustomerGoods::where('order_customer_id', $entity->id)->delete();
        }
        if (!empty($parameters['list_goods'])) {
            foreach ($parameters['list_goods'] as $goods) {
                OrderCustomerGoods::create([
                    'order_customer_id' => $entity->id,
                    'goods_type_id' => $goods['goods_type_id'],
                    'goods_unit_id' => $goods['goods_unit_id'],
                    'quantity' => $goods['quantity'],
                    'weight' => isset($goods['weight']) ? $goods['weight'] : 0,
                    'volume' => isset($goods['volume']) ? $goods['volume'] : 0,
                    'total_weight' => isset($goods['total_weight']) ? $goods['total_weight'] : 0,
                    'total_volume' => isset($goods['total_volume']) ? $goods['total_volume'] : 0,
                ]);
            }
        }
        if ($this->_beforeStatus != $entity->status) {
            OrderCustomerHistory::insert([
                'order_customer_id' => $entity->id,
                'status' => $entity->status,
                'reason' => $entity->reason
            ]);
        }

        $entity->goodsList = $parameters['list_goods'];

        app('App\Http\Controllers\Backend\OrderCustomerController')->_processCreateRelation(isset($parameters['id']) ? 'update' : 'save', $parameters, $entity);
    }

    // API lấy lịch sử bản ghi
    // CreatedBy nlhoang 03/06/2020
    public function getAuditing($id)
    {
        $item = DB::table('audits')
            ->join('admin_users', 'audits.user_id', '=', 'admin_users.id')
            ->where('auditable_id', '=', $id)
            ->where('auditable_type', '=', 'App\Model\Entities\OrderCustomer')
            ->orderBy('audits.created_at', 'ASC')
            ->get([
                'admin_users.username',
                'audits.event',
                'audits.old_values',
                'audits.new_values',
                'audits.created_at'
            ]);
        return $item;
    }

    /**
     * Lấy thông tin đơn hàng khách hàng trên BĐK ĐHKH
     * Created by ptly 2020.08.25
     * @param $fromDate ngày bắt đầu
     * @param $toDate ngày kết thúc
     * @return mixed
     */
    public function getOrderCustomerControlBoard($fromDate, $toDate)
    {
        $events = DB::select(DB::raw($this->buildQueryOrderCustomerControlBoard()), array(
            'fromDate_1' => $fromDate,
            'fromDate_2' => $fromDate,
            'fromDate_3' => $fromDate,
            'fromDate_4' => $fromDate,
            'fromDate_5' => $fromDate,
            'toDate_1' => $toDate,
            'toDate_2' => $toDate,
            'toDate_3' => $toDate,
            'toDate_4' => $toDate,
            'toDate_5' => $toDate,
        ));
        return $events;
    }

    private function buildQueryOrderCustomerControlBoard()
    {
        $userId = Auth::user()->id;
        $selectQuery = " SELECT DISTINCT
                        order_customer.code,
                        order_customer.order_no,
                        customer.id as resourceId,
                        order_customer.id as id,
                        customer.full_name AS customer_name,
                        order_customer.status AS status,
                        (CONCAT(order_customer.ETD_date,' ',order_customer.ETD_time)) AS start,
                        (CONCAT(order_customer.ETA_date,' ',order_customer.ETA_time)) AS end,
                        (CONCAT(order_customer.ETD_date_reality,' ',order_customer.ETD_time_reality)) AS real_start,
                        (CONCAT(order_customer.ETA_date_reality,' ',order_customer.ETA_time_reality)) AS real_end,
                        GROUP_CONCAT(orders.id SEPARATOR '||') as order_ids,
                        GROUP_CONCAT(orders.order_code SEPARATOR '||') as order_codes,
                        GROUP_CONCAT(orders.status SEPARATOR '||') as order_statuses
                        FROM
                        order_customer
                        LEFT JOIN `customer` ON `customer`.id = `order_customer`.`customer_id`
                        LEFT JOIN `orders` ON `orders`.`order_customer_id` = `order_customer`.`id`
                        LEFT JOIN `customer_group_customer` ON `customer_group_customer`.`customer_id` = `orders`.`customer_id` AND `customer_group_customer`.`del_flag` = 0
                        LEFT JOIN `admin_users_customer_group` ON `admin_users_customer_group`.`customer_group_id` = `customer_group_customer`.`customer_group_id` AND `admin_users_customer_group`.`del_flag` = 0 ";

        $whereQuery = "WHERE orders.del_flag = 0
                                AND order_customer.del_flag = 0
                                AND (( order_customer.ETD_date>= :fromDate_1 AND
                                        order_customer.ETA_date <= :toDate_1
                                    )
                                    OR (
                                            order_customer.ETD_date<= :fromDate_2 AND
                                            order_customer.ETA_date>= :toDate_2
                                        )
                                    OR (
                                            order_customer.ETD_date <= :fromDate_3 AND
                                            order_customer.ETA_date>= :fromDate_4 AND
                                            order_customer.ETA_date<= :toDate_3
                                        )
                                     OR (
                                            order_customer.ETD_date>= :fromDate_5 AND
                                            order_customer.ETD_date<= :toDate_4 AND
                                            order_customer.ETA_date>= :toDate_5
                                        ))
                                    AND (admin_users_customer_group.admin_user_id = $userId
                                    OR admin_users_customer_group.customer_group_id IS NULL)
                                    GROUP BY order_customer.id";

        $sql = $selectQuery . $whereQuery;
        return $sql;
    }
}
