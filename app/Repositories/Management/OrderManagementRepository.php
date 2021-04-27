<?php

namespace App\Repositories\Management;

use App\Common\AppConstant;
use App\Model\Entities\Order;
use App\Repositories\OrderRepository;
use App\Validators\OrderValidator;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use stdClass;

class OrderManagementRepository extends OrderRepository
{
    // API trả danh sách cho app mobile
    // CreatedBy nlhoang 19/05/2020
    public function getManagementItemList($request)
    {
        $status = $request['status'];
        $statusCollectedDocuments = $request['statusCollectedDocuments'];

        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $fromDate = $request['fromDate'];
        $toDate = $request['toDate'];
        $sorts = $request['sort'];
        $customers = $request['customers'];
        $drivers = $request["drivers"];
        $vehicles = $request["vehicles"];
        $userId = $request["userId"];
        $dateField = $request['dateField'] ? $request['dateField'] : 'ETD_date'; // Lọc thời gian theo trường nào

        $isDeleted = '0';

        if (!empty($request['isDeleted'])) {
            $isDeleted = $request['isDeleted'] == "true" ? '1' : '0';
        }

        $table_name = $this->getTableName();
        $query = DB::table($table_name)
            ->leftJoin('partner as p', $table_name . '.partner_id', '=', 'p.id')
            ->leftJoin('drivers', 'drivers.id', '=', $table_name . '.primary_driver_id')
            ->leftJoin('vehicle', 'vehicle.id', '=', $table_name . '.vehicle_id')
            ->leftJoin('customer', function ($join) {
                $join->on('customer.id', '=', 'orders.customer_id')
                    ->where('customer.del_flag', '=', 0);
            })
            ->leftJoin('customer_group_customer as cgc', function ($join) use ($table_name) {
                $join->on('cgc.customer_id', '=', $table_name . '.customer_id')
                    ->where('cgc.del_flag', '=', 0);
            })
            ->leftJoin('admin_users_customer_group as acg', function ($join) {
                $join->on('acg.customer_group_id', '=', 'cgc.customer_group_id')
                    ->where('acg.del_flag', '=', 0);
            })
            ->leftJoin('admin_users', 'admin_users.id', '=', $table_name . '.upd_id')
            ->where([
                [$table_name . '.del_flag', '=', $isDeleted],
            ])->where(function ($query) use ($userId) {
                $query->where('acg.admin_user_id', '=', $userId)
                    ->orWhereNull('acg.customer_group_id');
            });

        if (isset($status) && 0 < sizeof($status)) {
            $query->whereIn($table_name . '.status', $status);
        }

        if (isset($statusCollectedDocuments) && 0 < sizeof($statusCollectedDocuments)) {
            $query->whereIn($table_name . '.status_collected_documents', $statusCollectedDocuments);
        }

        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.customer_name', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.order_code', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.customer_mobile_no', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.vin_no', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.model_no', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.id', 'like', '%' . $textSearch . '%')
                    ->orWhere('vehicle.reg_no', 'like', '%' . $textSearch . '%')
                    ->orWhere('drivers.full_name', 'like', '%' . $textSearch . '%');
            });
        }
        if (!empty($fromDate)) {
            $query->whereDate($table_name . '.' . $dateField, '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate($table_name . '.' . $dateField, '<=', $toDate);
        }

        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                $query->orderBy($sort['sortField'], $sort['sortType']);
            }
        }
        if (!empty($customers)) {
            $query->whereIn($table_name . '.customer_id', $customers);
        }
        if (!empty($drivers)) {
            $query->whereIn('drivers.id', $drivers);
        }
        if (!empty($vehicles)) {
            $query->whereIn('vehicle.id', $vehicles);
        }

        // Luôn luôn ở cuối câu Query để đảm bảo Query check đúng..
        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $querySelected = $query;
            $query->whereNotIn($table_name . '.id', $ids);
        }
        $count = $query->groupBy($table_name . '.id')->get()->count();


        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $query->groupBy($table_name . '.id')->skip($offset)->take($pageSize);

        $columns = [
            $table_name . '.id as id',
            $table_name . '.id as key',
            $table_name . '.order_code',
            $table_name . '.order_no',
            $table_name . '.status',
            $table_name . '.order_date',
            $table_name . '.ETA_date',
            $table_name . '.ETD_date',
            $table_name . '.ETA_time',
            $table_name . '.ETD_time',
            $table_name . '.amount',
            $table_name . '.customer_name',
            $table_name . '.precedence',
            $table_name . '.status_collected_documents',
            $table_name . '.document_type',
            $table_name . '.is_collected_documents',
            $table_name . '.date_collected_documents',
            $table_name . '.time_collected_documents',
            $table_name . '.date_collected_documents_reality',
            $table_name . '.time_collected_documents_reality',
            $table_name . '.upd_date',
            $table_name . '.vin_no',
            $table_name . '.model_no',
            'customer.full_name as name_of_customer_id',
            'admin_users.username as name_of_upd_id',
            'vehicle.reg_no as name_of_vehicle_id',
            'drivers.full_name as name_of_driver_id',
            'p.full_name as name_of_partner_id',
        ];
        $items = $query->get($columns);

        // Thêm các id đã selected vào đầu chuỗi
        if ($pageIndex == 1 && !empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = $querySelected->whereIn($table_name . '.id', $ids)
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
        $result = [
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'items' => $items
        ];
        return $result;
    }

    // API lấy danh sách đơn hàng sẵn sàng cho chuyến xe
    // CreatedBy nlhoang 01/06/2020
    public function getOrderForRouteList($request)
    {
        $status = $request['status'];
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $ids = $request['ids'];
        $fromDate = $request['fromDate'];
        $toDate = $request['toDate'];
        $sorts = $request['sort'];

        $table_name = $this->getTableName();
        $query = DB::table($table_name)
            ->leftJoin('vehicle', 'vehicle.id', '=', $table_name . '.vehicle_id')
            ->leftJoin('customer', 'orders.customer_id', 'customer.id')
            ->where([
                [$table_name . '.del_flag', '=', '0'],
                ['customer.del_flag', '=', '0'],
                [$table_name . '.status', '=', '2'],
            ]);

        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.customer_name', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.order_code', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.customer_mobile_no', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.id', 'like', '%' . $textSearch . '%');
            });
        }
        if (!empty($fromDate)) {
            $query->whereDate('orders.ETD_date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate('orders.ETA_date', '<=', $toDate);
        }

        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                $query->orderBy($sort['sortField'], $sort['sortType']);
            }
        }

        // Luôn luôn ở cuối câu Query để đảm bảo Query check đúng..
        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $querySelected = $query;
            $query->whereNotIn($table_name . '.id', $ids);
        }
        $count = $query->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $query->skip($offset)->take($pageSize);
        $columns = [
            $table_name . '.id as id',
            $table_name . '.id as key',
            $table_name . '.order_code',
            $table_name . '.order_no',
            $table_name . '.status',
            $table_name . '.order_date',
            $table_name . '.ETA_date',
            $table_name . '.ETD_date',
            $table_name . '.amount',
            $table_name . '.customer_name',
            $table_name . '.precedence',
            $table_name . '.status_collected_documents',
            $table_name . '.document_type',
            $table_name . '.is_collected_documents',
            $table_name . '.date_collected_documents',
            $table_name . '.time_collected_documents',
            $table_name . '.date_collected_documents_reality',
            $table_name . '.time_collected_documents_reality',
            'customer.full_name as name_of_customer_id',
        ];
        $items = $query->get($columns);

        // Thêm các id đã selected vào đầu chuỗi
        if (!empty($ids) && 0 < sizeof($ids)) {
            $itemSelected = $querySelected->whereIn($table_name . '.id', $ids)
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
        $result = [
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'items' => $items
        ];
        return $result;
    }

    // API trả kết quả mobile
    // CreatedBy nlhoang 20/05/2020
    // Modified by ptly 21.07.2020 Chỉ lấy đơn hàng của khách hàng thuộc nhóm mà user quản lý hoặc
    // khách hàng không thuộc nhóm nào (Trường hợp xem chi tiết từ QR code)
    public function getDataByID($id, $userId = 0)
    {
        $table_name = $this->getTableName();
        $query = DB::table($table_name)
            ->leftJoin(
                DB::raw('(SELECT locations.id ,locations.latitude, locations.longitude, locations.full_address, locations.title FROM locations) as location_destination'),
                'location_destination.id',
                '=',
                'orders.location_destination_id'
            )
            ->leftJoin(
                DB::raw('(SELECT locations.id ,locations.latitude, locations.longitude, locations.full_address, locations.title FROM locations) as location_arrival'),
                'location_arrival.id',
                '=',
                'orders.location_arrival_id'
            )
            ->leftJoin('vehicle', $table_name . '.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('customer', $table_name . '.customer_id', 'customer.id')
            ->leftJoin('drivers', $table_name . '.primary_driver_id', '=', 'drivers.id')
            ->leftJoin('customer_group_customer as cgc', function ($join) use ($table_name) {
                $join->on('cgc.customer_id', '=', $table_name . '.customer_id')
                    ->where('cgc.del_flag', '=', 0);
            })
            ->leftJoin('admin_users_customer_group as acg', function ($join) {
                $join->on('acg.customer_group_id', '=', 'cgc.customer_group_id')
                    ->where('acg.del_flag', '=', 0);
            })
            ->where($table_name . '.id', '=', $id);

        if ($userId != 0) { // Trường hợp không truyền vào $userId thì bỏ đk where
            $query = $query->where(function ($query) use ($userId) {
                $query->where('acg.admin_user_id', '=', $userId)
                    ->orWhereNull('acg.customer_group_id');
            });
        }
        $item = $query
            ->get([
                $table_name . '.*',
                'customer.full_name as name_of_customer_id',
                'location_arrival.title as name_of_location_arrival_id',
                'location_destination.title as name_of_location_destination_id',
                'vehicle.id as vehicle_id',
                'vehicle.reg_no as name_of_vehicle_id',
                'drivers.id as primary_driver_id',
                'drivers.full_name as name_of_primary_driver_id',
                DB::raw($table_name . '.amount - coalesce(' . $table_name . '.commission_amount, 0 ) as final_amount')
            ])->first();

        if ($item != null) {
            $route = DB::table('orders')
                ->leftJoin('routes', 'routes.id', '=', 'orders.route_id')
                ->where('orders.id', '=', $id)
                ->get([
                    'routes.id as route_id',
                    'routes.name as name_of_route_id'
                ]);
            if ($route->first()) {
                $item->route_id = $route->first()->{'route_id'};
                $item->name_of_route_id = $route->first()->{'name_of_route_id'};
            }
            $item->goods = DB::table('order_goods')
                ->join('goods_type', 'order_goods.goods_type_id', '=', 'goods_type.id')
                ->leftJoin('goods_unit', 'order_goods.goods_unit_id', '=', 'goods_unit.id')
                ->where('order_goods.order_id', '=', $id)
                //            ->orderBy('order_goods.id', 'ASC')
                ->get([
                    'order_goods.*',
                    'goods_type.title as name_of_goods_type_id',
                    'goods_unit.title as name_of_goods_unit_id'
                ]);
            $item->locations = DB::table('order_locations')
                ->join('locations', 'order_locations.location_id', '=', 'locations.id')
                ->where('order_locations.order_id', '=', $id)
                ->orderBy('order_locations.type', 'ASC')
                ->get([
                    'order_locations.*',
                    'locations.title as name_of_location_id'
                ]);

            $item->files = DB::table($table_name)
                ->join('order_file as df', 'df.order_id', '=', $table_name . '.id')
                ->join('files', 'files.file_id', '=', 'df.file_id')
                ->where($table_name . '.id', '=', $id)
                ->where('df.del_flag', 0)
                ->get([
                    'df.*',
                    'files.file_name',
                    'files.file_type',
                    'files.path',
                ]);
            foreach ($item->files as $file) {
                $file->file_path = AppConstant::getImagePath($file->path, $file->file_type);
            }
        }

        return $item;
    }

    public function getFiles($id)
    {
        $table_name = $this->getTableName();
        $files = DB::table($table_name)
            ->join('order_file as df', 'df.order_id', '=', $table_name . '.id')
            ->join('files', 'files.file_id', '=', 'df.file_id')
            ->where($table_name . '.id', '=', $id)
            ->where('df.del_flag', 0)
            ->get([
                'df.*',
                'files.file_name',
                'files.file_type',
                'files.path',
            ]);
        foreach ($files as $file) {
            $file->file_path = AppConstant::getImagePath($file->path, $file->file_type);
        }


        return $files;
    }

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {
        $item = Order::find($id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid location id: ' . $id);
        }
    }

    // API lấy lịch sử bản ghi
    // CreatedBy nlhoang 03/06/2020
    public function getAuditing($id)
    {
        $item = DB::table('audits')
            ->join('admin_users', 'audits.user_id', '=', 'admin_users.id')
            ->where('auditable_id', '=', $id)
            ->where('auditable_type', '=', 'App\Model\Entities\Order')
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

    // API lấy lịch sử đơn hàng
    // CreatedBy nlhoang 03/06/2020
    public function getHistory($id)
    {
        $item = DB::table('order_history')
            ->where('order_history.order_id', '=', $id)
            ->orderBy('order_history.ins_date', 'ASC')
            ->get([
                'order_history.ins_date',
                'order_history.order_status',
                'order_history.current_location',
            ]);
        return $item;
    }

    // API lấy lộ trình đơn hàng
    // CreatedBy nlhoang 03/06/2020
    public function getRoute($id)
    {
        $ordersQuery = DB::table('orders')
            ->leftJoin(
                DB::raw('(SELECT locations.id, locations.full_address, locations.longitude, locations.latitude FROM locations) as location_destination'),
                'location_destination.id',
                '=',
                'orders.location_destination_id'
            )
            ->leftJoin(
                DB::raw('(SELECT locations.id, locations.full_address, locations.longitude, locations.latitude FROM locations) as location_arrival'),
                'location_arrival.id',
                '=',
                'orders.location_arrival_id'
            )
            ->leftJoin('vehicle', 'orders.vehicle_id', '=', 'vehicle.id')
            ->where([
                ['orders.id', '=', $id],
                ['orders.del_flag', '=', '0'],
                ['vehicle.del_flag', '=', '0']
            ]);
        $order = $ordersQuery->get([
            'orders.id as order_id', 'order_code', 'orders.status as status',
            'orders.ETD_date', 'orders.ETD_time', 'location_destination.full_address as location_destination', 'location_destination.longitude as location_destination_longitude', 'location_destination.latitude as location_destination_latitude',
            'orders.ETA_date', 'orders.ETA_time', 'location_arrival.full_address as location_arrival', 'location_arrival.longitude as location_arrival_longitude', 'location_arrival.latitude as location_arrival_latitude',
            'vehicle.latitude as current_latitude', 'vehicle.longitude as current_longitude', 'vehicle.current_location as current_location'
        ])->first();
        return $order;
    }

    // API lấy danh sách đơn hàng trên BDK
    // CreatedBy nlhoang 03/06/2020
    public function getOrderOnControlBoard($fromDate, $toDate, $vehicleTeamIDs, $vehicleIDs, $vehicleGroupIDs, $customerIDs)
    {
        $events = DB::select(
            DB::raw($this->buildQueryOrderOnControlBoard($vehicleTeamIDs, $vehicleIDs, $vehicleGroupIDs, $customerIDs)),
            array(
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
            )
        );
        return $events;
    }

    private function buildQueryOrderOnControlBoard($vehicleTeamIDs, $vehicleIDs, $vehicleGroupIDs, $customerIDs)
    {
        $selectQuery = "SELECT 
                        `vehicle`.`id` AS `vehicle_id`,
                        `vehicle`.`reg_no` AS `reg_no`,
                        d.id AS driver_id,
                        d.full_name AS full_name,
                        `orders`.`order_code` AS `name`,
                        `orders`.`status` AS `status`,
                        `orders`.`id` AS `orderId`,
                        CONCAT(orders.ETD_date_reality,
                                ' ',
                                orders.ETD_time_reality) AS real_start,
                        CONCAT(orders.ETA_date_reality,
                                ' ',
                                orders.ETA_time_reality) AS real_end,
                        CONCAT(orders.ETA_date, ' ', orders.ETA_time) AS end,
                        CONCAT(orders.ETD_date, ' ', orders.ETD_time) AS start
                        
                     ";
        $selectVehicleInfo = ' `vehicle`.`id` as vehicle_id, `vehicle`.`reg_no`, `vehicle`.`current_location` ';

        $selectQuery = $selectQuery . ',' . $selectVehicleInfo;

        $subQuery = "`vehicle`
                                             LEFT JOIN `driver_vehicle` ON `vehicle`.`id` = `driver_vehicle`.`vehicle_id`
                                             LEFT JOIN `driver_vehicle_team` ON `driver_vehicle`.`driver_id` = `driver_vehicle_team`.`driver_id`
                                             WHERE
                                               `driver_vehicle`.`del_flag` = 0
                                             AND  `vehicle`.`del_flag` = 0
                                             AND  `vehicle`.`active` = 1
                                             AND  `vehicle`.`status` != 4
             
             
                                             ";
        if (!empty($vehicleIDs)) {
            $subQuery .= " AND vehicle.id IN (" . $vehicleIDs . ")";
        };
        if (!empty($vehicleTeamIDs)) {
            $subQuery .= " AND `driver_vehicle_team`.`vehicle_team_id` IN (" . $vehicleTeamIDs . ")";
        }
        //  $joinOperator = ' LEFT JOIN ';
        $fromQuery = " FROM
        (SELECT DISTINCT 
                            `vehicle`.`id`, `vehicle`.`group_id`, `vehicle`.`reg_no`, `vehicle`.`current_location`
                        FROM
                            " . $subQuery . "
                            ) AS vehicle
          LEFT JOIN 
      `orders` ON `orders`.`vehicle_id` = `vehicle`.`id`
      LEFT JOIN drivers as d ON `orders`.`primary_driver_id` = d.`id`
                    ";

        $whereQuery = "WHERE `orders`.`del_flag` = 0
                            AND `orders`.`status` NOT IN (1,2)
                                AND (( orders.ETD_date>= :fromDate_1 AND
                                orders.ETA_date <= :toDate_1
                                    )
                                    OR (
                                        orders.ETD_date<= :fromDate_2 AND
                                        orders.ETA_date>= :toDate_2
                                        )
                                    OR (
                                        orders.ETD_date <= :fromDate_3 AND
                                        orders.ETA_date>= :fromDate_4 AND
                                        orders.ETA_date<= :toDate_3
                                        )
                                     OR (
                                        orders.ETD_date>= :fromDate_5 AND
                                        orders.ETD_date<= :toDate_4 AND
                                        orders.ETA_date>= :toDate_5
                                        ))";

        $sql = $selectQuery . $fromQuery . $whereQuery;
        if (!empty($vehicleGroupIDs)) {
            $sql .= " AND vehicle.group_id IN (" . $vehicleGroupIDs . ")";
        }
        // if (!empty($statues)) {
        //     $sql .= " AND orders.status IN (" . $statues . ")";
        // }
        if (!empty($customerIDs)) {
            $sql .= " AND orders.customer_id IN (" . $customerIDs . ")";
        }
        return $sql;
    }

    // API lấy danh sách đơn hàng trên Lệnh vận chuyển
    // CreatedBy nlhoang 04/06/2020
    public function getOrderOnOrderBoard($fromDate, $toDate)
    {
        $events = DB::select(DB::raw($this->buildQueryOrderOnOrderBoard()), array(
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

    private function buildQueryOrderOnOrderBoard()
    {
        $selectQuery = "SELECT 
                                orders.order_no,
                                GROUP_CONCAT(orders.id SEPARATOR '||') as order_ids,
                                GROUP_CONCAT(orders.order_code SEPARATOR '||') as order_codes,
                                vehicle.id as resourceId,
                                COUNT(1) AS total,
                                '' as model_no,
                                MIN(orders.id) as id,
                                SUM(CASE WHEN orders.status = 5 THEN 1 ELSE 0 END) AS total_status,
                                MIN(CONCAT(orders.ETD_date,' ',orders.ETD_time)) AS ETD_date,
                                MAX(CONCAT(orders.ETA_date,' ',orders.ETA_time)) AS ETA_date,
                                MIN(CONCAT(orders.ETD_date_reality,' ',orders.ETD_time_reality)) AS ETD_date_reality,
                                MAX(CONCAT(orders.ETA_date_reality,' ',orders.ETA_time_reality)) AS ETA_date_reality
                        FROM
                          vehicle
                            LEFT JOIN 
                        `orders` ON `orders`.`vehicle_id` = `vehicle`.`id`
                     ";

        $whereQuery = "WHERE `orders`.`del_flag` = 0
                                AND (( orders.ETD_date>= :fromDate_1 AND
                                orders.ETA_date <= :toDate_1
                                    )
                                    OR (
                                        orders.ETD_date<= :fromDate_2 AND
                                        orders.ETA_date>= :toDate_2
                                        )
                                    OR (
                                        orders.ETD_date <= :fromDate_3 AND
                                        orders.ETA_date>= :fromDate_4 AND
                                        orders.ETA_date<= :toDate_3
                                        )
                                     OR (
                                        orders.ETD_date>= :fromDate_5 AND
                                        orders.ETD_date<= :toDate_4 AND
                                        orders.ETA_date>= :toDate_5
                                        ))
                       GROUP BY coalesce(orders.order_no, orders.order_code),  IF(orders.order_no = '', orders.order_code, '')";

        $sql = $selectQuery . $whereQuery;
        $sql = "SELECT 
                        T.*,
                        T.order_no as title,
                         CASE WHEN T.total != T.total_status THEN 1
                                    ELSE  2  END  AS status,
                        ETD_date_reality as real_start,
                        ETA_date_reality as real_end,
                        ETD_date  as start,
                        ETA_date  as end  FROM (" . $sql . ") as T";
        return $sql;
    }

    /**
     * Lấy đơn hàng theo id
     * @param $id
     * @param $driverId
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|mixed|null|stdClass
     * Modified By Ptly 24.07.2020: Thêm tham số cho khớp với OrderRepository
     */
    public function getOrderById($id, $driverId = 0)
    {
        if (empty($id)) {
            return null;
        }
        return Order::find($id);
    }
}
