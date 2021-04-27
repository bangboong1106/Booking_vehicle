<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\Order;
use App\Model\Entities\Routes;
use App\Repositories\Traits\RouteExportTrait;
use App\Repositories\Traits\RoutePriceTrait;
use App\Repositories\Traits\RoutePayrollTrait;

use App\Repositories\Base\CustomRepository;
use App\Validators\RoutesValidator;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RoutesRepository extends CustomRepository
{
    use RouteExportTrait, RoutePriceTrait, RoutePayrollTrait;

    protected $_filterCustomerRole = true;

    function model()
    {
        return Routes::class;
    }

    public function getGroupColumn()
    {
        return 'route_status';
    }


    public function validator()
    {
        return RoutesValidator::class;
    }

    public function getCode()
    {
        return 'route_code';
    }

    protected function _withRelations($query)
    {
        return $query->with([]);
    }


    public function getRouteWithQuotaID($quotaId)
    {
        if (!$quotaId) {
            return null;
        }
        $this->_filterCustomerRole = false;

        return $this->search([
            'quota_id_eq' => $quotaId
        ])->get();
    }

    /**
     * @param $entity Routes
     * @param $data
     * @param bool $forUpdate
     * @return mixed
     */
    protected function _prepareRelation($entity, $data, $forUpdate = false)
    {
        $entity = parent::_prepareRelation($entity, $data, $forUpdate);
        $entity->listCost = isset($data['listCost']) ? $data['listCost'] : [];
        return $entity;
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        $columns = [
            '*',
            'v.reg_no as reg_no',
            'd.full_name as primary_driver_name',
            'ld.title as destination_location_title',
            'la.title as arrival_location_title',
        ];
        $this->_filterCustomerRole = true;
        return $this->search($query, $columns)->with(['costs'])->paginate($limit, ['*'], 'page', 1);
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
            'v.reg_no as reg_no',
            'd.full_name as primary_driver_name',
            'ld.title as name_of_location_destination_id',
            'la.title as name_of_location_arrival_id',
            'lpd.title as name_of_province_destination_id',
            'lpa.title as name_of_province_arrival_id',
            'ldd.title as name_of_district_destination_id',
            'lda.title as name_of_district_arrival_id',
            DB::raw('(routes.gps_distance / 1000) as gps_distance'),
            DB::raw('(SELECT SUM(amount_driver) FROM route_cost where route_id = ' . $this->getTableName() . '.id AND del_flag = 0) as total_cost_reality')
        ];
        $this->_filterCustomerRole = true;
        $queryBuilder = $this->search($query, $columns)->with(['insUser', 'updUser']);
        return $queryBuilder->paginate($perPage);
    }

    protected function getKeyValue()
    {
        return [
            'vehicle' => [
                'filter_field' => 'v.reg_no',
            ],
            'primary_driver' => [
                'filter_field' => 'd.full_name',
            ],
            'name_of_location_destination_id' => [
                'filter_field' => 'ld.title',
            ],
            'name_of_location_arrival_id' => [
                'filter_field' => 'la.title',
            ],

            'name_of_province_destination_id' => [
                'filter_field' => 'lpd.title',
            ],
            'name_of_province_arrival_id' => [
                'filter_field' => 'lpa.title',
            ],

            'name_of_district_destination_id' => [
                'filter_field' => 'ldd.title',
            ],
            'name_of_district_arrival_id' => [
                'filter_field' => 'lda.title',
            ],

            'ETD_date' => [
                'sort_field' => DB::raw('CONCAT(routes.ETD_date, "", routes.ETD_time)'),
                'is_sort_raw' => true
            ],
            'ETA_date' => [
                'sort_field' => DB::raw('CONCAT(routes.ETA_date, "", routes.ETA_time)'),
                'is_sort_raw' => true
            ],
            'ETD_date_reality' => [
                'sort_field' => DB::raw('CONCAT(routes.ETD_date_reality, "", routes.ETD_time_reality)'),
                'is_sort_raw' => true
            ],
            'ETA_date_reality' => [
                'sort_field' => DB::raw('CONCAT(routes.ETA_date_reality, "", routes.ETA_time_reality)'),
                'is_sort_raw' => true
            ],
            'name_of_ins_id' => [
                'filter_field' => 'ai.username'
            ],
            'name_of_upd_id' => [
                'filter_field' => 'au.username',
            ]
        ];
    }

    // Hàm build câu lệnh chuyến xe
    // CreatedBy nlhoang 31/08/2020
    protected function getQueryBuilder($columns)
    {
        $partnerId = Auth::user()->partner_id;
        $query = $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('vehicle as v', $this->getTableName() . '.vehicle_id', '=', 'v.id')
            ->leftJoin('drivers as d', $this->getTableName() . '.driver_id', '=', 'd.id')
            ->leftJoin('locations as ld', $this->getTableName() . '.location_destination_id', '=', 'ld.id')
            ->leftJoin('m_province as lpd', 'ld.province_id', '=', 'lpd.province_id')
            ->leftJoin('m_district as ldd', 'ld.district_id', '=', 'ldd.district_id')
            ->leftJoin('locations as la', $this->getTableName() . '.location_arrival_id', '=', 'la.id')
            ->leftJoin('m_province as lpa', 'la.province_id', '=', 'lpa.province_id')
            ->leftJoin('m_district as lda', 'la.district_id', '=', 'lda.district_id')
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id');

        /*if ($this->_filterCustomerRole && !empty(Auth::User())) {
            $customerIDs = DB::table('customer AS t1')
                ->leftJoin('customer_group_customer AS t2', 't2.customer_id', '=', 't1.id')
                ->leftJoin('admin_users_customer_group AS t3', 't3.customer_group_id', '=', 't2.customer_group_id')
                ->where('t1.del_flag', '=', 0)
                ->where(function ($query) {
                    $query->where(function ($q) {
                        $q->where('t3.admin_user_id', '=', Auth::User()->id)
                            ->where('t3.del_flag', '=', 0);
                    })
                        ->orWhereNull('t2.customer_id');
                })
                ->groupBy('t1.id')->pluck('t1.id as customer_id')->toArray();
            $nullCustomerID = 0;
            array_push($customerIDs, $nullCustomerID);
            $query->where(function ($q) use ($customerIDs) {
                $q->whereNull($this->getTableName() . ".customer_ids")
                    ->orWhereRaw($this->getTableName() . ".customer_ids REGEXP REPLACE('" . implode(',', $customerIDs) . "', ',', '(\\,|$)|') ");
            });
        }*/

        if ($partnerId) {
            $query->where($this->getTableName() . '.partner_id', '=', $partnerId);
        }

        $query->orderBy($this->getSortField(), $this->getSortType());
        return $query;
    }


    // Lấy gợi ý định mức nhiên liệu
    // nlhoang 17/3/2020
    // Định mức tiêu thụ nhiên liệu: quy chuẩn số lít / 100 KM
    // Công thức: Giá dầu * số KM khoảng cách * (định mức tiêu thụ nhiên liệu không hàng / 100)
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

    public function getRouteByOrder($order_id)
    {
        $result = DB::table('routes as r')
            ->leftJoin('orders as o', 'r.id', '=', 'o.route_id')
            ->where('o.id', '=', $order_id)
            ->where('o.del_flag', '=', 0)
            ->select('r.id', 'r.name', 'r.route_code')->first();
        return $result;
    }

    // Lấy dữ liệu Chuyến xe để xuất theo mẫu
    // CreatedBy nlhoang 10/04/2020
    public function getExportByIDs($ids, $template)
    {
        return $this->getDataForTemplateByID($ids, $template);
    }

    // Lấy danh sách đơn hàng của chuyến group theo khách hàng
    //CreatedBy nlhoang 01/07/2020
    public function getOrdersGroupByCustomerById($id)
    {
        return $this->groupOrderByCustomerByRouteId($id);
    }

    // Tính doanh thu theo báo giá
    //CreatedBy nlhoang 01/07/2020
    // ModifiedBy nlhoang 23/07/2020: bổ sung tính giá theo loại hàng hoá
    public function calcPrice($params)
    {
        return $this->calculatePrice($params);
    }

    // Tính lương tài xế theo từng đơn hàng
    //CreatedBy nlhoang 22/07/2020
    public function calcPayroll($params)
    {
        return $this->calculatePayroll($params);
    }

    //Lấy thông tin vin_no,model_no,location
    public function getExtendProperty($ids)
    {
        return $this->getExtendPropertyByIDs($ids);
    }

    // Lấy danh sách đơn hàng của ĐHKH
    public function getOrdersByID($id)
    {
        $result = Order::leftJoin('vehicle as v', 'v.id', '=', 'orders.vehicle_id')
            ->where('orders.route_id', '=', $id)
            ->where('orders.del_flag', '=', 0)
            ->get([
                'orders.*',
                'v.reg_no'
            ]);
        return $result;
    }

    //Lấy chuyến theo xe
    public function getRouteByVehicles($vehicleIds)
    {
        if (empty($vehicleIds)) {
            return [];
        }
        return DB::table('routes as r')
            ->where(['r.del_flag', '=', 0])
            ->whereIn('r.vehicle_id', $vehicleIds)
            ->orderBy('r.route_status', 'asc')
            ->orderBy('r.ETD_date', 'asc')
            ->get(['r.id', 'r.route_code', 'r.name', 'r.capacity_weight_ratio', 'r.capacity_volume_ratio']);
    }


    // Lấy danh sách xe theo khoảng thời gian
    public function getRouteListByVehicle($params)
    {
        $vehicleId = $params["vehicle_id"];
        $start = $params["start"];
        $end = $params["end"];
        return Routes::leftJoin('drivers', 'drivers.id', '=', 'routes.driver_id')
            ->where('routes.del_flag', '=', 0)
            ->where('routes.vehicle_id', $vehicleId)
            ->orderBy('routes.ETA_date', 'desc')
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($query) use ($start, $end) {
                    $query->where('routes.ETD_date', '>=', $start)
                        ->where('routes.ETA_date', '<=', $end);
                })
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('routes.ETD_date', '<=', $start)
                            ->where('routes.ETA_date', '>=', $end);
                    })
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('routes.ETD_date', '<=', $start)
                            ->where('routes.ETA_date', '>=', $start)
                            ->where('routes.ETA_date', '<=', $end);
                    })
                    ->orWhere(function ($query) use ($start, $end) {

                        $query->where('routes.ETD_date', '>=', $start)
                            ->where('routes.ETD_date', '<=', $end)
                            ->where('routes.ETA_date', '>=', $end);
                    });
            })
            ->get(['routes.*', 'drivers.full_name as driver_name']);
    }

    //Lấy danh sách chuyến của đơn
    public function getRoutesByOrders($orderIds)
    {
        if (empty($orderIds))
            return [];

        $result = DB::table('orders as o')
            ->join('routes as r', 'r.id', '=', 'o.route_id')
            ->where([
                ['o.del_flag', '=', 0],
                ['r.del_flag', '=', 0]
            ])
            ->whereIn('o.id', $orderIds)
            ->groupBy('r.id')
            ->select("r.route_code")->get();

        return $result;
    }

    //Tạo câu lệnh lấy chuyến xe trên BDK
    public function buildQueryForBoard($params)
    {
        $vehiclePageIndex = $params['vehiclePageIndex'];
        $vehiclePageSize = $params['vehiclePageSize'];
        $vehicleTeamIDs = $params['vehicleTeamIDs'];
        $vehicleGroupIDs = $params['vehicleGroupIDs'];
        $vehicleIDs = $params['vehicleIDs'];
        $customerIDs = $params['customerIDs'];
        $statuses = $params['statuses'];
        $start = $params['start'];
        $end = $params['end'];
        $partnerId = $params['partnerId'] != 0 ? $params['partnerId'] :  Auth::user()->partner_id;

        $statuses = implode(',', $statuses);
        $offset = ($vehiclePageIndex - 1) * $vehiclePageSize;



        $vehicleQuery = DB::table('vehicle as v')
            ->where('v.active', '=', 1)
            ->where('v.status', '!=', 4)
            ->where('v.del_flag', '=', 0);

        $vehicleQuery = $vehicleQuery->leftJoin('driver_vehicle as dv', 'v.id', '=', 'dv.vehicle_id')
            ->leftJoin('driver_vehicle_team as dvt', 'dv.driver_id', '=', 'dvt.driver_id');

        if (!empty($vehicleIDs)) {
            $vehicleQuery->whereIn('v.id', explode(',', $vehicleIDs));
        }
        if (!empty($vehicleTeamIDs)) {
            $vehicleQuery->whereIn('dvt.vehicle_team_id', explode(',', $vehicleTeamIDs));
        }
        if ($partnerId) {
            $vehicleQuery->where('v.partner_id', '=', $partnerId);
        }
        $totalResource = $vehicleQuery->count(DB::raw('distinct v.id'));


        $totalVehicleIDs = $vehicleQuery
            ->groupBy('v.id')
            ->get('v.id')
            ->pluck('id')
            ->toArray();

        $vIDs = $vehicleQuery
            ->groupBy('v.id')
            ->orderBy('v.reg_no')
            ->skip($offset)
            ->take($vehiclePageSize)
            ->get('v.id')
            ->pluck('id')
            ->toArray();

        $columns = [
            'o.route_status AS status',
            'o.id',
            'o.vehicle_id AS resourceId',
            DB::raw('exists (select 1 from route_file where route_file.route_id = o.id and route_file.del_flag = 0 limit 1) as is_attachment'),
            DB::raw('CONCAT(o.route_code, "|", o.name) AS title'),
            DB::raw('CONCAT(o.ETD_date_reality, " ", o.ETD_time_reality) AS real_start'),
            DB::raw('CONCAT(o.ETA_date_reality, " ", o.ETA_time_reality) AS real_end'),
            DB::raw('CONCAT(o.ETA_date, " ", o.ETA_time) AS end'),
            DB::raw('CONCAT(o.ETD_date, " ", o.ETD_time) AS start'),
            DB::raw('CASE WHEN (o.route_status = 0) THEN
                                "#678bfb"
                            WHEN (o.route_status = 1) THEN
                                "#52bb56"
                            WHEN (o.route_status = 2) THEN
                                "#1d2124"
                            else "" END color')
        ];

        $orderQuery = DB::table('routes as o')
            ->leftJoin('vehicle as v', 'v.id', '=', 'o.vehicle_id')
            ->where('o.del_flag', '=', 0)
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->where('o.ETD_date', '>=', $start)
                        ->where('o.ETA_date', '<=', $end);
                })->orWhere(function ($q) use ($start, $end) {
                    $q->where('o.ETD_date', '<=', $start)
                        ->where('o.ETA_date', '>=', $end);
                })->orWhere(function ($q) use ($start, $end) {
                    $q->where('o.ETD_date', '<=', $start)
                        ->where('o.ETA_date', '>=', $start)
                        ->where('o.ETA_date', '<=', $end);
                })->orWhere(function ($q) use ($start, $end) {
                    $q->where('o.ETD_date', '>=', $start)
                        ->where('o.ETD_date', '<=', $end)
                        ->where('o.ETA_date', '>=', $end);
                });
            });
        if (!empty($vehicleGroupIDs)) {
            $orderQuery->whereIn('v.group_id', explode(',', $vehicleGroupIDs));
        }
        if (!empty($statuses)) {
            $orderQuery->whereIn('o.route_status', explode(',', $statuses));
        }
        if (!empty($customerIDs)) {
            $orderQuery->whereRaw("o.customer_ids REGEXP REPLACE('" . implode(',', $customerIDs) . "', ',', '(\\,|$)|') ");
        }

        if ($partnerId) {
            $orderQuery->where('o.partner_id', '=', $partnerId);
        }

        $groupQuery = clone $orderQuery;

        $result = $orderQuery->whereIn('o.vehicle_id', $vIDs)->select($columns)->get();
        $group = $groupQuery->whereIn('o.vehicle_id', $totalVehicleIDs)->groupBy('o.route_status')
            ->select([
                'o.route_status as status',
                DB::raw('COUNT(o.id) as total')
            ])->get();

        return [
            'totalResource' => $totalResource,
            'resourceIDs' => $vIDs,
            'result' => $result,
            'group' => $group

        ];
    }

    public function getItemsByUserID($all, $q, $user_id, $partner_id, $params)
    {
        $vehicleID = $params['vehicleID'];
        $driverID = $params['driverID'];

        $query = Routes::select("id", 'route_code', "name as title", "final_cost")
            ->where(function ($q) {
                $q->where('name', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('route_code', 'LIKE', '%' . request('q') . '%');
            })->where(function ($q) use ($vehicleID) {
                if ($vehicleID) {
                    $q->orWhere('routes.vehicle_id', '=', $vehicleID);
                }
            })
            ->where(function ($q) use ($driverID) {
                if ($driverID) {
                    $q->orWhere('routes.driver_id', '=', $driverID);
                }
            });
        if (!empty($partner_id)) {
            $query->where('routes.partner_id', '=', $partner_id);
        }
        $query->orderBy('upd_date', 'desc')
            ->groupBy('routes.id')
            ->paginate(20);
        return $query;
    }

    // Khoá chuyến xe
    //CreatedBy nlhoang 29/09/2020
    public function lock($type, $fromDate, $toDate)
    {
        $this->_doLock('lock', $type, $fromDate, $toDate);
    }

    // Mở khoá chuyến xe
    //CreatedBy nlhoang 29/09/2020
    public function unlock($type, $fromDate, $toDate)
    {
        $this->_doLock('unlock', $type, $fromDate, $toDate);
    }

    private function _doLock($lockType, $type, $fromDate, $toDate)
    {
        $lockType = $lockType == 'lock' ? 1 : 0;
        $query = Routes::where('del_flag', '=', 0);
        switch ($type) {
            case 1:
                $query->where('ETD_date', '>=', $fromDate)
                    ->where('ETD_date', '<=', $toDate);
                break;
            case 2:
                $query->where('ETD_date_reality', '>=', $fromDate)
                    ->where('ETD_date_reality', '<=', $toDate);
                break;
            case 3:
                $query->where('ETA_date', '>=', $fromDate)
                    ->where('ETA_date', '<=', $toDate);
                break;
            case 4:
                $query->where('ETA_date_reality', '>=', $fromDate)
                    ->where('ETA_date_reality', '<=', $toDate);
                break;
            default:
                $query->where('ETA_date_reality', '>=', $fromDate)
                    ->where('ETA_date_reality', '<=', $toDate);
                break;
        }

        $query->update(['is_lock' => $lockType]);
    }

    public function getRouteByRouteCodes($routeCodes = [])
    {
        if (empty($routeCodes)) {
            return [];
        }

        return $this->search(['route_code_in' => $routeCodes])->with('costs')->get();
    }
}
