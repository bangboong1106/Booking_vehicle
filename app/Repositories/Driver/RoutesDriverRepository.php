<?php

namespace App\Repositories\Driver;

use App\Common\AppConstant;
use App\Model\Entities\ReceiptPayment;
use App\Model\Entities\RouteCost;
use App\Model\Entities\RouteFile;
use App\Model\Entities\Routes;
use App\Repositories\RoutesRepository;
use App\Validators\RoutesValidator;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RoutesDriverRepository extends RoutesRepository
{

    //Lấy tổng số đơn trong chuyến và tổng đơn hoàn thành trong chuyến
    // CreatedBy nlhoang 07/08/2020
    public function getTotalOrdersOnRoute($route_id)
    {
        // Lấy tổng số đơn của mỗi chuyến
        $item = DB::table('routes as r')
            ->join('orders as o', 'o.route_id', '=', 'r.id')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('count(case when o.status = 5 then 1 end) AS total_complete')
            )
            ->groupBy('r.id')
            ->where('o.route_id', '=', $route_id)
            ->first();

        return $item;
    }

    public function countRouteByDriverId($driverId)
    {
        $queryCount = DB::table('routes as r')
            ->where([
                ['r.driver_id', '=', $driverId],
                ['r.del_flag', '=', '0']
            ])
            ->groupBy('r.route_status')
            ->selectRaw('count(*) as count, r.route_status as status')
            ->get();
        return $queryCount;
    }

    public function getRoutesByDriverIdAndStatus($driverId, $request)
    {
        $status = $request['status'];
        $pageSize = $request['pageSize'];
        $pageIndex = $request['pageIndex'];
        $textSearch = $request['textSearch'];
        $fromDate = $request['fromDate'];
        $toDate = $request['toDate'];
        $sorts = $request['sort'];
        $isApproved = $request['isApproved'];

        $queryCount = DB::table('routes as r')
            ->whereIn('r.route_status', $status)
            ->where([
                ['r.driver_id', '=', $driverId],
                ['r.del_flag', '=', '0']
            ]);

        $routesQuery = DB::table('routes as r')
            ->leftJoin(
                DB::raw('(SELECT locations.id ,locations.latitude, locations.longitude, locations.full_address, locations.title FROM locations) as location_destination'),
                'location_destination.id',
                '=',
                'r.location_destination_id'
            )
            ->leftJoin(
                DB::raw('(SELECT locations.id ,locations.latitude, locations.longitude, locations.full_address, locations.title FROM locations) as location_arrival'),
                'location_arrival.id',
                '=',
                'r.location_arrival_id'
            )
            ->leftJoin('vehicle', 'vehicle.id', '=', 'r.vehicle_id')
            ->where([
                ['r.driver_id', '=', $driverId],
                ['r.del_flag', '=', '0']
            ])
            ->whereIn('r.route_status', $status);

        if (!empty($textSearch)) {
            $queryCount->where(function ($query) use ($textSearch) {
                $query->where('r.name', 'like', '%' . $textSearch . '%')
                    ->orWhere('r.route_code', 'like', '%' . $textSearch . '%');
            });
            $routesQuery->where(function ($query) use ($textSearch) {
                $query->where('r.name', 'like', '%' . $textSearch . '%')
                    ->orWhere('r.route_code', 'like', '%' . $textSearch . '%');
            });
        }
        if (!empty($fromDate)) {
            $queryCount->whereDate('r.ETD_date', '>=', $fromDate);
            $routesQuery->whereDate('r.ETD_date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $queryCount->whereDate('r.ETA_date', '<=', $toDate);
            $routesQuery->whereDate('r.ETA_date', '<=', $toDate);
        }
        if (isset($isApproved)) {
            $queryCount->where('r.is_approved', '=', $isApproved);
            $routesQuery->where('r.is_approved', '=', $isApproved);
        }

        if (!empty($sorts)) {
            foreach ($sorts as $sort) {
                if (!isset($sort['sortField'])) {
                    continue;
                }
                $sortField = $sort['sortField'];
                if (Str::startsWith($sortField, 'routes.')) {
                    $sortField =  str_replace("routes.", "r.", $sortField);
                }
                $routesQuery->orderBy($sortField, $sort['sortType']);
            }
        }

        $count = $queryCount->count();
        $totalPage = 0;
        if (0 < $count) {
            $totalPage = (int)(($count + $pageSize - 1) / $pageSize);
        }
        $offset = ($pageIndex - 1) * $pageSize;

        $routesQuery->skip($offset)->take($pageSize);
        $items = $routesQuery->get([
            'r.id',
            'r.id as route_id',
            'r.name as name',
            'r.route_code',
            'r.route_status',
            'r.ETD_date',
            'r.ETD_time',
            'location_destination.full_address as location_destination',
            'location_destination.latitude as latitude_destination',
            'location_destination.longitude as longitude_destination',
            'location_destination.title as title_destination',
            'r.ETA_date',
            'r.ETA_time',
            'location_arrival.full_address as location_arrival',
            'location_arrival.latitude as latitude_arrival',
            'location_arrival.longitude as longitude_arrival',
            'location_arrival.title as title_arrival',
            'vehicle.reg_no as reg_no',
            'r.is_approved as is_approved',
            'r.count_order as total_order'
        ]);

        $result = [
            'totalPage' => $totalPage,
            'totalCount' => $count,
            'items' => $items,
        ];

        return $result;
    }

    public function getItemInfoById($id)
    {
        return $this->findFirstOrNew(['id' => $id]);
    }

    public function getItemById($id)
    {
        $query = Routes::leftJoin(
            DB::raw('(SELECT locations.id ,locations.latitude, locations.longitude, locations.full_address, locations.title FROM locations) as location_destination'),
            'location_destination.id',
            '=',
            'routes.location_destination_id'
        )
            ->leftJoin(
                DB::raw('(SELECT locations.id ,locations.latitude, locations.longitude, locations.full_address, locations.title FROM locations) as location_arrival'),
                'location_arrival.id',
                '=',
                'routes.location_arrival_id'
            )
            ->leftJoin('vehicle', 'vehicle.id', '=', 'routes.vehicle_id')
            ->where([
                ['routes.id', '=', $id],
                ['routes.del_flag', '=', '0']
            ]);


        $item = $query->select([
            'routes.id',
            'routes.id as route_id',
            'routes.name as name',
            'routes.route_code',
            'routes.route_status',
            'routes.ETD_date',
            'routes.ETD_time',
            'location_destination.full_address as location_destination',
            'location_destination.latitude as latitude_destination',
            'location_destination.longitude as longitude_destination',
            'location_destination.title as title_destination',
            'routes.ETA_date',
            'routes.ETA_time',
            'location_arrival.full_address as location_arrival',
            'location_arrival.latitude as latitude_arrival',
            'location_arrival.longitude as longitude_arrival',
            'location_arrival.title as title_arrival',
            'vehicle.reg_no as reg_no',
            'routes.is_approved as is_approved',
            'routes.count_order as total_order'
        ])->first();

        return $item;
    }

    // Lấy bảng tổng quan chi phí
    // CreatedBy nlhoang 25/09/2020
    public function getCostOverviewReport($driver_id, $fromDate, $toDate)
    {

        $start = $fromDate;
        $end = $toDate;
        $routeCostTable = RouteCost::getTableName();
        $costTable = ReceiptPayment::getTableName();

        // Lấy ra chi phí của tài xế từ các chuyến xe đã hoàn thành và được phê duyệt
        $result  = DB::table($this->getTableName() . ' as r')
            ->leftJoin($routeCostTable . ' as rc', 'r.id', '=', 'rc.route_id')
            ->leftJoin($costTable . ' as c', 'c.id', '=', 'rc.receipt_payment_id')
            ->leftJoin('drivers as d', 'd.id', '=', 'r.driver_id')
            ->where('r.del_flag', '=', 0)
            ->where('r.route_status', '=', 1)
            ->where('r.is_approved', '=', 1)
            ->where('c.del_flag', '=', 0)
            ->where('c.is_display_driver', '=', 1)
            ->where('d.user_id', '=', $driver_id)
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->where('r.ETD_date_reality', '>=', $start)
                        ->where('r.ETA_date_reality', '<=', $end);
                });
            })
            ->groupBy('c.name')
            ->select([
                'c.name',
                DB::raw('SUM(rc.amount) as amount')
            ])->get();
        return $result;
    }

    //Nếu chuyến có đơn hoàn thành thì không cho hủy chuyến
    public function checkConditionCancelRoute($id)
    {
        $result = DB::table('routes as r')
            ->join('orders as o', 'o.route_id', '=', 'r.id')
            ->where([
                ['r.id', '=', $id],
                ['o.status', '=', config('constant.HOAN_THANH')],
                ['r.del_flag', '=', 0],
                ['o.del_flag', '=', 0]
            ])
            ->groupBy('r.id')
            ->select(DB::raw("COUNT(o.id) total_order_complete"))->first();
        if ($result && $result->total_order_complete > 0) {
            return false;
        }

        return true;
    }

    //Nếu chuyến có đơn chưa đến thời gian trả hàng thì không cho hoàn thành chuyến
    // CreatedBy nlhoang 01/10/2020
    public function checkConditionCompleteRoute($id, $time)
    {
        $result = DB::table('routes as r')
            ->join('orders as o', 'o.route_id', '=', 'r.id')
            ->where([
                ['r.id', '=', $id],
                ['o.status', '=', config('constant.DANG_VAN_CHUYEN')],
                ['r.del_flag', '=', 0],
                ['o.del_flag', '=', 0]
            ])
            ->select([
                DB::raw("DATE_ADD(CAST(CONCAT(o.ETD_date_reality, ' ', o.ETD_time_reality) as datetime), INTERVAL " . $time . " MINUTE) as ETD")
            ])
            ->get();
        if ($result) {
            $currentFormat = 'Y-m-d H:i:s';
            $now = Carbon::now();

            $orders = ($result->filter(function ($value) use ($now, $currentFormat) {
                if ($value->ETD) {
                    $reality = Carbon::createFromFormat($currentFormat, $value->ETD);
                    return $now->lt($reality);
                }
                return false;
            }));
            if ($orders->count() > 0) {
                return false;
            }
            return true;
        }

        return true;
    }
}
