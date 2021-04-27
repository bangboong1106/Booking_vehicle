<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Input;
use Yajra\Datatables\Datatables;
use Yajra\DataTables\Utilities\Request;
use App\Model\Entities\Customer;
use App\Model\Entities\Order;
use App\Model\Entities\Routes;
use App\Model\Entities\Vehicle;
use App\Model\Entities\Driver;
use App\Model\Entities\GoodsGroup;


/**
 * Class RoleController
 * @package App\Http\Controllers\Backend
 */
class QuickSearchController extends BackendController
{
    /**
     * RoleController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return view('eloquent.object');
    }

    /**
     * Process dataTable ajax response.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function order(Request $request)
    {
        $skip = $request->get('start') != null ? $request->get('start') : 0;
        $take = $request->get('length') != null ? $request->get('length') : 10;
        $routeId = Request::get('route_id');
        $vehicleId = Request::get('vehicle_id');
        $driverId = Request::get('driver_id');
        $ids = Request::get('exceptIds');
        $ids = explode(',', $ids);

        $partnerId = Auth::user()->partner_id;

        try {
            $q = DB::table('orders as o')
                ->select(
                    "o.id",
                    "o.order_code as order_code",
                    "o.order_no as order_no",
                    "o.customer_name as customer_name",
                    "o.customer_mobile_no as customer_mobile_no",
                    "o.status as status",
                    "o.status_partner as status_partner",
                    "o.precedence as precedence"
                )
                ->leftJoin('customer_group_customer as cgc', function ($join) {
                    $join->on('cgc.customer_id', '=', 'o.customer_id')
                        ->where('cgc.del_flag', '=', 0);
                })
                ->leftJoin('admin_users_customer_group as aucg', function ($join) {
                    $join->on('aucg.customer_group_id', '=', 'cgc.customer_group_id')
                        ->where('aucg.del_flag', '=', 0);
                })->where('o.del_flag', '=', 0) // Lấy đơn không xóa
                ->whereNotIn('o.id', $ids)
                ->where(function ($qv) use ($routeId, $vehicleId, $driverId) {
                    $qv->whereIn('o.status', [config('constant.KHOI_TAO'), config('constant.SAN_SANG')])
                        ->orWhere(function ($q) use ($routeId, $vehicleId, $driverId) {
                            $q->where(function ($q) use ($routeId) {
                                $q->whereNull('o.route_id');
                                if ($routeId) {
                                    $q->orWhere('o.route_id', '=', (int)$routeId);
                                }
                            })->where(function ($q) use ($vehicleId) {
                                if ($vehicleId) {
                                    $q->orWhere('o.vehicle_id', '=', (int)$vehicleId);
                                }
                            })
                                ->where(function ($q) use ($driverId) {
                                    if ($driverId) {
                                        $q->where('o.primary_driver_id', '=', (int)$driverId);
                                    }
                                });
                        });
                })
                ->where(function ($query) {
                    $query->where('aucg.admin_user_id', '=', Auth::User()->id)
                        ->orWhereNull('aucg.customer_group_id');
                })
                ->where(function ($query) {
                    $query->where('o.order_code', 'like', '%' . request('search.value') . '%');
                });

            if ($partnerId) {
                $q = $q->where('o.partner_id', '=', $partnerId);
            }

            $q = $q->distinct();
            $count = $q->count();

            $items = $q->orderBy('o.upd_date', 'desc')
                ->skip($skip)->take($take);

            return Datatables::of($items)->setTotalRecords($count)->make(true);
        } catch (Exception $e) {
            logError($e);
        }
    }

    public function vehicle(Request $request)
    {
        $skip = $request->get('start') != null ? $request->get('start') : 0;
        $take = $request->get('length') != null ? $request->get('length') : 10;
        $all = $request->get('all');
        $distance = $request->get('isDistance');
        $ids = Request::get('exceptIds');

        $locationType = $request->get('locationType');
        $destinationLocationId = $request->get('destinationLocationId');
        $arrivalLocationId = $request->get('arrivalLocationId');
        try {
            if (empty($distance)) {
                $ids = explode(',', $ids);
                if (empty($all)) {
                    $currentUserId = $this->getCurrentUser()->id;
                    $query = DB::table('vehicle')
                        ->select(
                            "vehicle.id",
                            "vehicle.reg_no as reg_no",
                            "vehicle.current_location",
                            "vehicle.volume",
                            "vehicle.weight",
                            "vehicle.length",
                            "vehicle.width",
                            "vehicle.height"
                        )
                        ->leftJoin('driver_vehicle', 'vehicle.id', '=', 'driver_vehicle.vehicle_id')
                        ->leftJoin('driver_vehicle_team', 'driver_vehicle.driver_id', '=', 'driver_vehicle_team.driver_id')
                        ->leftJoin('admin_users_vehicle_teams', 'driver_vehicle_team.vehicle_team_id', '=', 'admin_users_vehicle_teams.vehicle_team_id')
                        ->where('vehicle.del_flag', '=', 0)
                        ->where('driver_vehicle.del_flag', '=', 0)
                        ->whereNotIn('vehicle.id', $ids)
                        ->where(function ($query) {
                            $query->where('reg_no', 'like', '%' . request('search.value') . '%');
                        });

                    if ($this->getCurrentUser()->role == 'partner') {
                        $query->where('partner_id', $this->getCurrentUser()->partner_id);
                    }
                    $count = $query->distinct()->count();

                    $items = $query->distinct()->orderBy('reg_no', 'asc')
                        ->skip($skip)->take($take);

                    return Datatables::of($items)->setTotalRecords($count)->make(true);
                } else {
                    $query = DB::table('vehicle')
                        ->select(
                            "vehicle.id",
                            "vehicle.reg_no as reg_no",
                            "vehicle.current_location",
                            "vehicle.volume",
                            "vehicle.weight",
                            "vehicle.length",
                            "vehicle.width",
                            "vehicle.height"
                        )
                        ->where('del_flag', '=', 0)
                        ->whereNotIn('vehicle.id', $ids)
                        ->where(function ($query) {
                            $query->where('reg_no', 'like', '%' . request('search.value') . '%');
                        });
                    $count = $query->count();

                    $items = $query->orderBy('reg_no', 'asc')
                        ->skip($skip)->take($take);

                    return Datatables::of($items)->setTotalRecords($count)->make(true);
                }
            } else {
                $currentUserId = $this->getCurrentUser()->id;
                if ($locationType == 1) {
                    $location = DB::table('locations')->where('id', '=', $destinationLocationId)->get(["latitude", "longitude"])->first();
                } else {
                    $location = DB::table('locations')->where('id', '=', $arrivalLocationId)->get(["latitude", "longitude"])->first();
                }
                $latitude = empty($location) ? 0 : $location->latitude;
                $longitude = empty($location) ? 0 : $location->longitude;
                $query = "FROM vehicle AS z
                      join driver_vehicle on z.id = driver_vehicle.vehicle_id
                      join driver_vehicle_team on driver_vehicle.driver_id = driver_vehicle_team.driver_id
                      join admin_users_vehicle_teams on driver_vehicle_team.vehicle_team_id = admin_users_vehicle_teams.vehicle_team_id
                      JOIN (
                            SELECT  " . $latitude . "  AS latpoint,  " . $longitude . " AS longpoint,
                                    10000000 AS radius,      111.045 AS distance_unit
                        ) AS p ON 1=1
                   WHERE 
                      admin_users_vehicle_teams.admin_user_id =" . $currentUserId . "
                      AND z.del_flag = 0
                      AND driver_vehicle.del_flag = 0 ";

                if ($ids != '') {
                    $query = $query . ' AND z.id not in (' . $ids . ') ';
                }
                $count = collect(DB::select("SELECT COUNT(distinct z.id) as total " . $query))->first()->total;

                $items = DB::select(" SELECT distinct z.id,
                        z.reg_no,
                        z.volume,
                        z.weight,
                        z.length,
                        z.width,
                        z.height,
                        z.latitude,
                        z.longitude,
                        p.distance_unit
                         * DEGREES(ACOS(COS(RADIANS(p.latpoint))
                         * COS(RADIANS(z.latitude))
                         * COS(RADIANS(p.longpoint) - RADIANS(z.longitude))
                         + SIN(RADIANS(p.latpoint))
                         * SIN(RADIANS(z.latitude)))) AS distance_in_km " . $query . " ORDER BY distance_in_km, z.reg_no");
                return Datatables::of($items)->setTotalRecords($count)->make(true);
            }
        } catch (Exception $e) {
            logError($e);
        }
    }

    public function driver(Request $request)
    {
        $skip = $request->get('start') != null ? $request->get('start') : 0;
        $take = $request->get('length') != null ? $request->get('length') : 10;
        $all = Request::get('all');
        $ids = Request::get('exceptIds');
        $ids = explode(',', $ids);

        $currentUser = $this->getCurrentUser();
        $partnerId = empty(Request::get('partner_id')) ? $currentUser->partner_id : Request::get('partner_id');

        try {
            if (empty($all)) {
                $id = $this->getCurrentUser()->id;
                $query = DB::table('drivers');
                // comment vì: tài xế k trong đội xe thì k hiện

                // ->join('driver_vehicle_team', 'drivers.id', '=', 'driver_vehicle_team.driver_id')
                // ->join('admin_users_vehicle_teams', 'driver_vehicle_team.vehicle_team_id', '=', 'admin_users_vehicle_teams.vehicle_team_id')
                // ->where('admin_users_vehicle_teams.admin_user_id', '=', $id)

                if ($currentUser->role == 'partner') {
                    $query->where('drivers.partner_id', '=', $partnerId);
                }
            } else {
                $query = DB::table('drivers');
            }
            $query->where('del_flag', '=', 0)
                ->whereNotIn('id', $ids)
                ->where(function ($query) {
                    $query->where('mobile_no', 'like', '%' . request('search.value') . '%')
                        ->orWhere('code', 'like', '%' . request('search.value') . '%')
                        ->orWhere('full_name', 'like', '%' . request('search.value') . '%');
                })
                ->select("drivers.id", "drivers.full_name", "drivers.mobile_no", "drivers.code", "drivers.driver_license", "drivers.partner_id");

            $count = $query->count();

            $items = $query->skip($skip)->take($take)
                ->orderBy('full_name', 'asc');

            return Datatables::of($items)->setTotalRecords($count)->make(true);
        } catch (Exception $e) {
            logError($e);
        }
    }

    public function contact(Request $request)
    {
        $skip = $request->get('start') != null ? $request->get('start') : 0;
        $take = $request->get('length') != null ? $request->get('length') : 10;
        $ids = Request::get('exceptIds');
        $ids = explode(',', $ids);
        try {
            $query = DB::table('contact')
                ->select(
                    "contact.id",
                    "contact.phone_number",
                    "contact.email",
                    "contact.contact_name",
                    "locations.id as location_id",
                    "locations.full_address",
                    "locations.title as location_title"
                )
                ->leftJoin('locations', function ($join) {
                    $join->on('locations.id', '=', 'contact.location_id');
                    $join->where('locations.del_flag', '=', 0);
                })
                ->where('contact.del_flag', '=', 0)
                ->where('contact.active', '=', 1)
                ->whereNotIn('contact.id', $ids)
                ->where(function ($query) {
                    $query->where('contact_name', 'like', '%' . request('search.value') . '%')
                        ->orWhere('email', 'like', '%' . request('search.value') . '%')
                        ->orWhere('phone_number', 'like', '%' . request('search.value') . '%');;
                });
            $count = $query->count();

            $items = $query->skip($skip)->take($take)
                ->orderBy('contact_name', 'asc');

            return Datatables::of($items)->setTotalRecords($count)->make(true);
        } catch (Exception $e) {
            logError($e);
        }
        return null;
    }

    public function customer(Request $request)
    {
        $skip = $request->get('start') != null ? $request->get('start') : 0;
        $take = $request->get('length') != null ? $request->get('length') : 10;
        $ids = Request::get('exceptIds');
        $ids = explode(',', $ids);

        $all = Request::get('all');

        $query = DB::table('customer')
            ->whereNotIn('customer.id', $ids)
            ->where(function ($query) {
                $query->where('customer_code', 'like', '%' . request('search.value') . '%')
                    ->orWhere('mobile_no', 'like', '%' . request('search.value') . '%')
                    ->orWhere('full_name', 'like', '%' . request('search.value') . '%');
            });
        if (empty($all)) {
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
            $query->whereIn('customer.id', $customerIDs);
        }
        $query->where('customer.del_flag', '=', '0');
        $count = $query->count();

        $items = $query->select(['customer.id', 'customer.customer_code', 'customer.full_name', 'customer.mobile_no'])
            ->skip($skip)->take($take)->orderBy('customer_code', 'asc');
        return Datatables::of($items)->setTotalRecords($count)->make(true);
    }

    public function vehicleTeam(Request $request)
    {
        $skip = $request->get('start') != null ? $request->get('start') : 0;
        $take = $request->get('length') != null ? $request->get('length') : 10;
        $ids = Request::get('exceptIds');
        $ids = explode(',', $ids);

        $currentUser = $this->getCurrentUser();
        $partnerId = empty(Request::get('partner_id')) ? $currentUser->partner_id : Request::get('partner_id');

        $query = DB::table('vehicle_team')
            ->where('del_flag', '=', 0)
            ->whereNotIn('id', $ids)
            ->where(function ($query) {
                $query->where('code', 'like', '%' . request('search.value') . '%')
                    ->orWhere('name', 'like', '%' . request('search.value') . '%');
            });

        if (empty($all) && $currentUser->role == 'partner') {
            $query->where('partner_id', $partnerId);
        }

        $count = $query->count();

        $items = $query->skip($skip)->take($take)
            ->orderBy('code', 'asc');

        return Datatables::of($items)->setTotalRecords($count)->make(true);
    }

    public function routes(Request $request)
    {
        $vehicleId = $request->get('vehicle_id');
        $driverId = $request->get('driver_id');
        $orderIds = $request->get('order_ids');

        $skip = $request->get('start') != null ? $request->get('start') : 0;
        $take = $request->get('length') != null ? $request->get('length') : 10;
        $ids = Request::get('exceptIds');
        $ids = explode(',', $ids);

        $currentUser = $this->getCurrentUser();
        $partnerId = empty(Request::get('partner_id')) ? $currentUser->partner_id : Request::get('partnerId');

        $query = DB::table('routes')
            ->leftJoin('vehicle', 'vehicle.id', '=', 'routes.vehicle_id')
            ->leftJoin('drivers', 'drivers.id', '=', 'routes.driver_id')
            ->where('routes.del_flag', '=', 0)
            ->where('routes.partner_id', '=', $partnerId)
            ->whereNotIn('routes.id', $ids)
            ->select([
                'routes.*',
                'vehicle.reg_no as reg_no',
                'drivers.full_name  as driver_name'
            ])
            ->where(function ($query) {
                $query->where('routes.route_code', 'like', '%' . request('search.value') . '%')
                    ->orWhere('routes.name', 'like', '%' . request('search.value') . '%')
                    ->orWhere('vehicle.reg_no', 'like', '%' . request('search.value') . '%')
                    ->orWhere('drivers.full_name', 'like', '%' . request('search.value') . '%');
            })
            ->where(function ($q) use ($vehicleId) {
                if ($vehicleId) {
                    $q->orWhere('routes.vehicle_id', '=', $vehicleId);
                }
            })
            ->where(function ($q) use ($driverId) {
                if ($driverId) {
                    $q->orWhere('routes.driver_id', '=', $driverId);
                }
            });
        $count = $query->count();
        $query->skip($skip)
            ->take($take);

        $columns = ($request->get('columns'));
        $orderable_column = collect($columns)->filter(function ($column) {
            return $column["orderable"] == true;
        })->first();
        if (empty($orderable_column)) {
            $query->orderBy('routes.upd_date', 'desc');
        }
        $items = $query;

        return Datatables::of($items)->setTotalRecords($count)->make(true);
    }

    public function quota(Request $request)
    {
        $skip = $request->get('start') != null ? $request->get('start') : 0;
        $take = $request->get('length') != null ? $request->get('length') : 10;
        $ids = Request::get('exceptIds');
        $ids = explode(',', $ids);
        $query = DB::table('quota')
            ->where('del_flag', '=', 0)
            ->whereNotIn('id', $ids)
            ->where(function ($query) {
                $query->where('quota_code', 'like', '%' . request('search.value') . '%')
                    ->orWhere('name', 'like', '%' . request('search.value') . '%');
            });
        $count = $query->count();

        $items = $query->skip($skip)
            ->take($take)
            ->orderBy('quota_code', 'asc');

        return Datatables::of($items)->setTotalRecords($count)->make(true);
    }

    public function goods(Request $request)
    {
        $ids = Request::get('exceptIds');
        $ids = explode(',', $ids);
        $skip = $request->get('start') != null ? $request->get('start') : 0;
        $take = $request->get('length') != null ? $request->get('length') : 10;
        $goodsGroupId = Request::get('goodsGroupId');
        $customerId = Request::get('customerId');

        if (!empty($goodsGroupId)) {
            $goodsGroup = GoodsGroup::where('id', '=', $goodsGroupId)->first();

            $fullGoodsGroupId = DB::table('goods_group')
                ->whereBetween('lidx', [$goodsGroup->lidx, $goodsGroup->ridx])
                ->get('id')
                ->pluck('id')
                ->toArray();
        }
        $query = DB::table('goods_type as gt')
            ->leftJoin('goods_group as gg', 'gt.goods_group_id', '=', 'gg.id')
            ->leftJoin('goods_unit as gn', 'gt.goods_unit_id', '=', 'gn.id')
            ->where('gt.del_flag', '=', 0)
            ->whereNotIn('gt.id', $ids)
            ->where(function ($query) {
                $query->where('gt.title', 'like', '%' . request('search.value') . '%')
                    ->orWhere('gt.code', 'like', '%' . request('search.value') . '%');
            });
        if (!empty($goodsGroupId)) {
            $query->whereIn('gt.goods_group_id', $fullGoodsGroupId);
        }
        if (!empty($customerId)) {
            $query->where('gt.customer_id', '=', $customerId);
        }

        $count = $query->count();
        $items = $query->skip($skip)->take($take)
            ->orderBy('title', 'asc')
            ->select('gt.id', 'gt.code', 'gt.title', 'gg.name as name_of_goods_group_id', 'gt.volume', 'gt.weight', 'gt.note',
                DB::raw('CONCAT(gn.code,"|",gn.title)as goods_unit'));

        return Datatables::of($items)->setTotalRecords($count)->make(true);
    }

    public function location(Request $request)
    {
        $customerId = Request::get('c_id', -1);
        $ids = Request::get('exceptIds');
        $ids = explode(',', $ids);
        $skip = $request->get('start') != null ? $request->get('start') : 0;
        $take = $request->get('length') != null ? $request->get('length') : 10;

        $query = DB::table('locations')
            ->where('del_flag', '=', 0)
            ->whereNotIn('id', $ids)
            ->where(function ($query) {
                $query->where('title', 'like', '%' . request('search.value') . '%')
                    ->orWhere('code', 'like', '%' . request('search.value') . '%');
            });

        if ($customerId > 0) {
            $query = $query->where('customer_id', $customerId);
        }

        $count = $query->count();
        $items = $query->skip($skip)->take($take)
            ->orderBy('code', 'asc');

        return Datatables::of($items)->setTotalRecords($count)->make(true);
    }

    public function orderCustomer(Request $request)
    {
        $ids = Request::get('exceptIds');
        $ids = explode(',', $ids);
        $skip = $request->get('start') != null ? $request->get('start') : 0;
        $take = $request->get('length') != null ? $request->get('length') : 10;

        try {
            $q = DB::table('orders')
                ->select(
                    "orders.id",
                    "orders.order_code as order_code",
                    "orders.order_no as order_no",
                    "orders.customer_name as customer_name",
                    "orders.customer_mobile_no as customer_mobile_no",
                    "orders.status as status",
                    "orders.precedence as precedence"
                )
                ->leftJoin('customer_group_customer as cgc', function ($join) {
                    $join->on('cgc.customer_id', '=', 'orders.customer_id')
                        ->where('cgc.del_flag', '=', 0);
                })
                ->leftJoin('admin_users_customer_group as aucg', function ($join) {
                    $join->on('aucg.customer_group_id', '=', 'cgc.customer_group_id')
                        ->where('aucg.del_flag', '=', 0);
                })
                ->where('orders.del_flag', '=', 0) // Lấy đơn không xóa
                ->whereNotIn('orders.id', $ids)
                ->where(function ($query) {
                    $query->where('orders.order_code', 'like', '%' . request('search.value') . '%');
                })
                ->where(function ($query) {
                    $query->where('aucg.admin_user_id', '=', Auth::User()->id)
                        ->orWhereNull('aucg.customer_group_id');
                })
                ->distinct();
            $count = $q->count();

            $items = $q->orderBy('orders.upd_date', 'desc')
                ->skip($skip)->take($take);

            return Datatables::of($items)->setTotalRecords($count)->make(true);
        } catch (Exception $e) {
            logError($e);
        }
    }

    public function fullSearch()
    {
        $term = Request::get('term');

        $customers = Customer::select('id', 'full_name as title', 'customer_code as description', DB::raw('"1" as type'))
            ->where('customer_code', 'LIKE', '%' . $term . '%')
            ->orWhere('full_name', 'LIKE', '%' . $term . '%')
            ->take(5);

        $vehicles = Vehicle::select('id', 'reg_no as title', DB::raw('"" as description'), DB::raw('"2" as type'))
            ->where('reg_no', 'LIKE', '%' . $term . '%')
            ->take(5);
        $drivers = Driver::select('id', 'full_name as title', DB::raw('"" as description'), DB::raw('"4" as type'))
            ->where('full_name', 'LIKE', '%' . $term . '%')
            ->take(5);
        $routes = Routes::select('id', 'route_code as title', DB::raw('name as description'), DB::raw('"5" as type'))
            ->where('route_code', 'LIKE', '%' . $term . '%')
            ->orWhere('name', 'LIKE', '%' . $term . '%')
            ->take(5);

        $orders = Order::select('id', 'order_no as title', 'order_code as description', DB::raw('"3" as type'))
            ->where('order_code', 'LIKE', '%' . $term . '%')
            ->orWhere('order_no', 'LIKE', '%' . $term . '%')
            ->take(5)
            ->union($customers)
            ->union($vehicles)
            ->union($drivers)
            ->union($routes);


        $datas = $orders->get();
        $results = array();
        foreach ($datas as $data) {
            $link = '';
            $value = '';
            switch ($data->type) {
                case 1:
                    $value = $data->title . ' - ' . $data->description;
                    $link = route('customer.show', $data->id);
                    break;
                case 2:
                    $value = $data->title;
                    $link = route('vehicle.show', $data->id);
                    break;
                case 3:
                    $value = $data->title . ' - ' . $data->description;
                    $link = route('order.show', $data->id);
                    break;
                case 4:
                    $value = $data->title . ' - ' . $data->description;
                    $link = route('driver.show', $data->id);
                    break;
                case 5:
                    $value = $data->title . ' - ' . $data->description;
                    $link = route('route.show', $data->id);
                    break;
            }
            $results[] = [
                'id' => $data->id,
                'value' => $value,
                'link' => $link,
                'type' => $data->type
            ];
        }
        return response()->json($results);
    }
}
