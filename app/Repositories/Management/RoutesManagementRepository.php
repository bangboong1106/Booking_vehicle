<?php

namespace App\Repositories\Management;

use App\Common\AppConstant;
use App\Model\Entities\Order;
use App\Model\Entities\RouteFile;
use App\Model\Entities\Routes;
use App\Repositories\RoutesRepository;
use App\Validators\RoutesValidator;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class RoutesManagementRepository extends RoutesRepository
{

    // API lấy thông tin chuyến xe
    // CreatedBy nlhoang 19/05/2020
    public function getManagementItemList($request)
    {
        $status = $request['status'];
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $fromDate = $request['fromDate'];
        $toDate = $request['toDate'];
        $ids = $request['ids'];
        $sorts = $request['sort'];
        $drivers = $request['drivers'];
        $vehicles = $request['vehicles'];
        $dateField = $request['dateField'] ? $request['dateField'] : 'ETD_date'; // Lọc thời gian theo trường nào

        $table_name = $this->getTableName();
        $query = DB::table($table_name)
            ->leftJoin('partner as p', $table_name . '.partner_id', '=', 'p.id')
            ->leftJoin('vehicle', $table_name . '.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('drivers', $table_name . '.driver_id', '=', 'drivers.id')
            ->where([
                [$table_name . '.del_flag', '=', '0'],
            ]);

        // Loại bỏ các id đã selected gửi lên.
        if (!empty($ids) && 0 < sizeof($ids)) {
            $query->whereNotIn($table_name . '.id', $ids);
        }
        if (isset($status) && 0 < sizeof($status)) {
            $query->whereIn($table_name . '.route_status', $status);
        }
        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.route_code', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.name', 'like', '%' . $textSearch . '%');
            });
        }

        if (!empty($fromDate)) {
            $query->whereDate($table_name . '.' . $dateField, '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate($table_name . '.' . $dateField, '<=', $toDate);
        }

        if (!empty($drivers)) {
            $query->whereIn('drivers.id', $drivers);
        }
        if (!empty($vehicles)) {
            $query->whereIn('vehicle.id', $vehicles);
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
            'vehicle.reg_no as name_of_vehicle_id',
            'drivers.full_name as name_of_driver_id',
            'p.full_name as name_of_partner_id',
            $table_name . '.id as key'
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
            ->leftJoin('quota', $table_name . '.quota_id', '=', 'quota.id')
            ->leftJoin('vehicle', $table_name . '.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('drivers', $table_name . '.driver_id', '=', 'drivers.id')
            ->where($table_name . '.id', '=', $id)
            ->get([
                $table_name . '.*',
                'quota.name as name_of_quota_id',
                'vehicle.reg_no as name_of_vehicle_id',
                'drivers.full_name as name_of_driver_id',
            ])->first();

        $item->orders = \DB::table($table_name . ' as q')
            ->leftJoin('orders as o', 'o.route_id', '=', 'q.id')
            ->where('q.id', $id)
            ->where('o.del_flag', '=', 0)
            ->orderBy('o.ins_date')
            ->get([
                'o.id as order_id',
                'o.order_code as name_of_order_id',
            ]);


        $item->locations = \DB::table($table_name . ' as q')
            ->leftJoin('orders as o', 'o.route_id', '=', 'q.id')
            ->leftJoin('locations as ld', 'ld.id', '=', 'o.location_destination_id')
            ->leftJoin('locations as la', 'la.id', '=', 'o.location_arrival_id')
            ->where('q.id', $id)
            ->where('o.del_flag', '=', 0)
            ->orderBy('o.ins_date')
            ->get([
                'o.*',
                'ld.title as name_of_destination_location_id',
                'la.title as name_of_arrival_location_id'
            ]);

        $item->costs = \DB::table('m_receipt_payment as rp')
            ->leftJoin('route_cost as dv', 'dv.receipt_payment_id', '=', 'rp.id')
            ->leftJoin($table_name . ' as q', 'dv.route_id', '=', 'q.id')
            ->where('q.id', $id)
            ->where(function ($query) {
                $query->where('dv.amount', '<>', 0)
                    ->orWhere('dv.amount_driver', '<>', 0)
                    ->orWhere('dv.amount_admin', '<>', 0);
            })
            ->get([
                'dv.*',
                'rp.name as name_of_receipt_payment_id'
            ]);
        $item->files = DB::table($table_name)
            ->join('route_file as df', 'df.route_id', '=', $table_name . '.id')
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
        return $item;
    }

    // API xoá
    // CreatedBy nlhoang 20/05/2020
    public function deleteDataByID($id)
    {

        $item = Routes::find($id);
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
            ->where('auditable_type', '=', 'App\Model\Entities\Routes')
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

    // API lấy lịch sử phê duyệt của chuyến xe
    // CreatedBy nlhoang 22/06/2020
    public function getApprovedHistory($id)
    {
        $item = DB::table('route_approval_history')
            ->join('admin_users', 'route_approval_history.approved_id', '=', 'admin_users.id')
            ->where('route_approval_history.route_id', '=', $id)
            ->orderBy('route_approval_history.ins_date', 'ASC')
            ->get([
                'route_approval_history.*',
                'admin_users.username as name_of_approved_id',

            ]);
        return $item;
    }

    /**
     * Api lấy danh sách chi phí chuyến xe
     * Created by ptly 2020.06.23
     */
    public function getRouteCost($id)
    {
        $entity = Routes::find($id);
        foreach ($entity->costs as $i => &$cost) {
            if ((empty($cost->amount_admin) || $cost->amount_admin == 0)
                && (empty($cost->amount_driver) || $cost->amount_driver == 0)
                && (empty($cost->amount) || $cost->amount == 0)
            ) {
                unset($entity->costs[$i]);
                $cost->delete();
                continue;
            }
            $cost->receipt_payment_name = $cost->receiptPayment ? $cost->receiptPayment->name : $cost->receipt_payment_name;
            $costFiles = DB::table('route_file as rf')
                ->join('files', 'rf.file_id', '=', 'files.file_id')
                ->where([
                    ['rf.route_id', '=', $id],
                    ['rf.cost_id', '=', $cost->receipt_payment_id],
                    ['rf.del_flag', '=', 0]
                ])
                ->get(['files.file_name', 'files.path', 'files.file_type']);
            foreach ($costFiles as $file) {
                $file->file_path = AppConstant::getImagePath($file->path, $file->file_type);
            }
            $cost->files = $costFiles;
        }
        $listCost = $entity->costs->toArray();
        $fuelCost = $this->getFuelCostHint($id);
        $approveNote = $entity->approved_note;
        return ['listCost' => $listCost, 'fuelCost' => $fuelCost, 'note' => $approveNote];
    }

    public function getFuelCostHint($routeId)
    {
        $total = DB::select('
            select (coalesce(sc.cost, 0) * coalesce(q.distance, 0) * (coalesce(vi.max_fuel, 0) / 100)) as cost_fuel
            from routes r
            left join vehicle v on v.id = r.vehicle_id
            left join vehicle_general_info vi on vi.vehicle_id = v.id
            left join quota q on q.id = r.quota_id
            join (select cast(`value` as decimal(18,4)) as cost from `system_config` where `key` =\'Cost.Fuel\') as sc
            where r.id = ' . $routeId . ' limit 1;');
        return empty($total) ? 0 : $total[0]->cost_fuel;
    }

    /**
     * Lấy thông tin chuyến xe trên lệnh vận chuyển
     * @param $fromDate từ ngày
     * @param $toDate đến ngày
     * @return mixed
     * created by ptly on 2020.08.15
     */
    public function getRouteControlBoard($fromDate, $toDate)
    {
        $events = DB::select(DB::raw($this->buildQueryRouteControlBoard()), array(
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

    private function buildQueryRouteControlBoard()
    {
        $userId = Auth::user()->id;
        $selectQuery = "SELECT DISTINCT routes.id, route_code, routes.name, routes.route_status, vehicle.reg_no, drivers.full_name
                       FROM routes
                       LEFT JOIN vehicle ON  vehicle.id = routes.vehicle_id
                       LEFT JOIN drivers ON drivers.id = routes.driver_id
                       LEFT JOIN driver_vehicle_team AS dvt ON dvt.driver_id = drivers.id
                       LEFT JOIN admin_users_vehicle_teams AS auvt ON auvt.vehicle_team_id = dvt.vehicle_team_id
                       LEFT JOIN orders AS o ON o.route_id = routes.id
                       LEFT JOIN customer_group_customer
                         ON customer_group_customer.customer_id = o.customer_id AND customer_group_customer.del_flag = 0
                       LEFT JOIN admin_users_customer_group
                         ON admin_users_customer_group.customer_group_id = customer_group_customer.customer_group_id AND
                            admin_users_customer_group.del_flag = 0 ";
        $whereQuery = " WHERE routes.del_flag = 0
                      AND o.del_flag = 0
                      AND (( routes.ETD_date>= :fromDate_1 AND
                                        routes.ETA_date <= :toDate_1
                                    )
                                    OR (
                                            routes.ETD_date<= :fromDate_2 AND
                                            routes.ETA_date>= :toDate_2
                                        )
                                    OR (
                                            routes.ETD_date <= :fromDate_3 AND
                                            routes.ETA_date>= :fromDate_4 AND
                                            routes.ETA_date<= :toDate_3
                                        )
                                     OR (
                                            routes.ETD_date>= :fromDate_5 AND
                                            routes.ETD_date<= :toDate_4 AND
                                            routes.ETA_date>= :toDate_5
                                        ))
                      AND (admin_users_customer_group.admin_user_id = $userId
                             OR admin_users_customer_group.customer_group_id IS NULL)
                      AND (auvt.admin_user_id = $userId)";
        $sql = $selectQuery . $whereQuery;
        return $sql;
    }
}
