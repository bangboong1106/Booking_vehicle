<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\Order;
use App\Model\Entities\AdminUsersCustomerGroup;
use App\Model\Entities\OrderGoods;
use App\Repositories\Traits\OrderExportTrait;
use App\Repositories\Base\CustomRepository;
use App\Validators\OrderValidator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class OrderRepository extends CustomRepository
{
    use OrderExportTrait;

    protected $_fieldsSearch = ['order_no', 'customer_name', 'customer_mobile_no', 'status', 'ETD_date', 'ETA_date', 'note'];

    protected $_filterCustomerRole = true;

    function model()
    {
        return Order::class;
    }

    public function getGroupColumn()
    {
        return 'status';
    }

    public function validator()
    {
        return OrderValidator::class;
    }

    protected function _withRelations($query)
    {
        return $query->with(['listGoods', 'vehicle', 'primaryDriver', 'secondaryDriver']);
    }

    /**
     * @param $entity Order
     * @param $data
     * @param bool $forUpdate
     * @return mixed
     */
    protected function _prepareRelation($entity, $data, $forUpdate = false)
    {
        $entity = parent::_prepareRelation($entity, $data, $forUpdate);
        empty($entity->ETD_date_reality) ? $entity->ETD_date_reality = null : null;
        empty($entity->ETA_date_reality) ? $entity->ETA_date_reality = null : null;

        empty($entity->ETD_time_reality) ? $entity->ETD_time_reality = null : null;
        empty($entity->ETA_time_reality) ? $entity->ETA_time_reality = null : null;

        $entity->goods = isset($data['goods']) ? $data['goods'] : [];

        if (isset($data['locationDestinations'])) {
            $locationDestination = Arr::first($data['locationDestinations']);
            $entity->location_destination_id = isset($locationDestination['location_id']) ? $locationDestination['location_id'] : 0;
            $entity->ETD_date = isset($locationDestination['date']) ? $locationDestination['date'] : null;
            $entity->ETD_time = isset($locationDestination['time']) ? $locationDestination['time'] : null;
            $entity->ETD_date_reality = isset($locationDestination['date_reality']) ? $locationDestination['date_reality'] : null;
            $entity->ETD_time_reality = isset($locationDestination['time_reality']) ? $locationDestination['time_reality'] : null;
            $entity->locationDestinations = isset($data['locationDestinations']) ? $data['locationDestinations'] : [];
        }

        if (isset($data['locationArrivals'])) {
            $locationArrival = Arr::first($data['locationDestinations']);
            $entity->location_arrival_id = isset($locationArrival['location_id']) ? $locationArrival['location_id'] : 0;
            $entity->ETA_date = isset($locationArrival['date']) ? $locationArrival['date'] : null;
            $entity->ETA_time = isset($locationArrival['time']) ? $locationArrival['time'] : null;
            $entity->ETA_date_reality = isset($locationArrival['date_reality']) ? $locationArrival['date_reality'] : null;
            $entity->ETA_time_reality = isset($locationArrival['time_reality']) ? $locationArrival['time_reality'] : null;
            $entity->locationArrivals = isset($data['locationArrivals']) ? $data['locationArrivals'] : [];
        }

        return $entity;
    }

    public function getOrdersByIds($ids = [])
    {
        if (empty($ids)) {
            return [];
        }
        $this->_filterCustomerRole = false;

        return $this->search([
            'id_in' => $ids,
            'sort_type' => 'asc',
            'sort_field' => 'id'
        ], [
            'id', 'id as order_id', 'order_code', 'ld.id as location_destination_id', 'ld.title as location_destination_title',
            'la.id as location_arrival_id', 'la.title as location_arrival_title', 'ETD_date', 'ETD_time', 'ETA_date', 'ETA_time',
            'amount', 'status', 'ETD_date_reality', 'ETD_time_reality', 'ETA_date_reality', 'ETA_time_reality', 'note', 'orders.cod_amount', 'orders.bill_no',
            'is_collected_documents', 'status_collected_documents', 'date_collected_documents', 'time_collected_documents',
            'orders.customer_id', 'weight', 'volume', 'is_insured_goods',
            'vin_no', 'model_no', 'commission_amount', 'customer_id', 'route_id', 'order_customer_id', 'is_lock', 'note',
            'partner_id', 'primary_driver_id', 'vehicle_id'
        ])->get();
    }

    public function getOrdersByOrderCodes($orderCodes = [])
    {
        if (empty($orderCodes)) {
            return [];
        }
        $this->_filterCustomerRole = false;

        return $this->search(['order_code_in' => $orderCodes], [
            'id',
            'order_code',
            'ld.id as location_destination_id',
            'ld.title as location_destination_title',
            'la.id as location_arrival_id',
            'la.title as location_arrival_title',
            'ETD_date',
            'ETD_time',
            'ETA_date',
            'ETA_time',
            'amount',
            'status',
            'ETD_date_reality',
            'ETD_time_reality',
            'ETA_date_reality',
            'ETA_time_reality',
            'weight',
            'volume',
            'commission_amount',
            'vin_no', 'model_no',
            'customer_id',
            'route_id',
            'order_customer_id',
            'is_lock',
            'vehicle_id',
            'primary_driver_id',
            'bill_no',
            'note',
            'order_no',
            'note',
            'partner_id'
        ])->get();
    }

    public function getOrdersByOrderCode($orderCode)
    {
        if (empty($orderCode)) {
            return null;
        }
        $this->_filterCustomerRole = false;

        return $this->search(['order_code_eq' => $orderCode], [
            'id',
            'order_code',
            'ld.id as location_destination_id',
            'ld.title as location_destination_title',
            'la.id as location_arrival_id',
            'la.title as location_arrival_title',
            'ETD_date',
            'ETD_time',
            'ETA_date',
            'ETA_time',
            'amount',
            'status',
            'ETD_date_reality',
            'ETD_time_reality',
            'ETA_date_reality',
            'ETA_time_reality',
            'weight',
            'volume',
            'commission_amount',
            'vin_no', 'model_no',
            'customer_id',
            'route_id',
            'order_customer_id',
            'is_lock',
            'vehicle_id',
            'primary_driver_id',
            'bill_no',
            'note',
            'order_no',
            'note',
            'partner_id'
        ])->first();
    }

    public function getListForOrderBoard($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $query['sort_type'] = 'asc';
        $query['sort_field'] = 'precedence';
        $this->_filterCustomerRole = true;
        $queryBuilder = $this->search($query, []);
        $tableName = $this->getTableName();
        $keyword = '';
        if (!empty($query['keyword'])) {
            $keyword = $query['keyword'];
        }
        $queryBuilder->where(function ($query) use ($tableName, $keyword) {
            $query->where($tableName . '.order_code', 'LIKE', '%' . $keyword . '%')
                ->orWhere($tableName . '.customer_name', 'LIKE', '%' . $keyword . '%')
                ->orWhere('ld.title', 'LIKE', '%' . $keyword . '%')
                ->orWhere('la.title', 'LIKE', '%' . $keyword . '%');
        })->where($tableName . '.status', config('constant.SAN_SANG', -1));

        $queryBuilder->with(['locationArrival', 'locationDestination']);
        $queryBuilder->orderBy('ETD_date', 'ASC');
        $queryBuilder->orderBy('ETD_time', 'ASC');

        return $this->_withRelations($queryBuilder)->paginate($perPage);
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 50));
        $columns = [
            '*',
            'ld.title as name_of_location_destination_id',
            'la.title as name_of_location_arrival_id',
            'ai.username as ai_username',
            'ai.full_name as ai_full_name',
            'au.username as au_username',
            'au.full_name as au_full_name',
            'customer.full_name as customer_full_name',
            'client.full_name as name_of_client_id',
            'v.reg_no as reg_no',
            'd.full_name as driver_name',
            'op.payment_type',
            DB::raw('CASE WHEN op.payment_type = 1 THEN "Chuyển khoản" ELSE "Tiền mặt" END as payment_type_title'),
            'pu.username as pu_username',
            'pu.full_name as pu_full_name',
            'op.goods_amount',
            'op.vat',
            'op.anonymous_amount',
            DB::raw('(orders.gps_distance / 1000) as gps_distance'),
            'lpd.title as name_of_province_destination_id',
            'lpa.title as name_of_province_arrival_id',
            'ldd.title as name_of_district_destination_id',
            'lda.title as name_of_district_arrival_id',
            'vg.name as name_of_vehicle_group_id',
            'p.full_name as name_of_partner_id',
        ];

        $this->_filterCustomerRole = true;
        $query = $this->handleStatusSearch($query);
        $queryBuilder = $this->search($query, $columns)->with(['insUser', 'updUser']);
        return $queryBuilder->paginate($perPage);
    }

    public function getListForHistory($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $this->_filterCustomerRole = true;

        $query = $this->handleStatusSearch($query);

        $queryBuilder = $this->search($query, []);
        return $this->_withRelations($queryBuilder)->paginate($perPage);
    }

    public function handleStatusSearch($query)
    {
        if (isset($query['status_eq'])) {
            $statusValue = $query['status_eq'];
            switch ($statusValue) {
                case 8:
                    $query['status_eq'] = 1;
                    $query['status_partner_eq'] = config('constant.PARTNER_CHO_GIAO_DOI_TAC_VAN_TAI');
                    break;
                case 9:
                    $query['status_eq'] = 1;
                    $query['status_partner_eq'] = config('constant.PARTNER_CHO_XAC_NHAN');
                    break;
                case 10:
                    $query['status_eq'] = 1;
                    $query['status_partner_eq'] = config('constant.PARTNER_YEU_CAU_SUA');
                    break;
            }
        }
        return $query;
    }

    protected function getKeyValue()
    {
        return [
            'reg_no' => [
                'filter_field' => 'v.reg_no',
            ],
            'vehicle' => [
                'filter_field' => 'v.reg_no',
            ],
            'driver_name' => [
                'filter_field' => 'd.full_name',
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
                'sort_field' => DB::raw('CONCAT(orders.ETD_date, "", orders.ETD_time)'),
                'is_sort_raw' => true
            ],
            'ETA_date' => [
                'sort_field' => DB::raw('CONCAT(orders.ETA_date, "", orders.ETA_time)'),
                'is_sort_raw' => true
            ],
            'ETD_date_reality' => [
                'sort_field' => DB::raw('CONCAT(orders.ETD_date_reality, "", orders.ETD_time_reality)'),
                'is_sort_raw' => true
            ],
            'ETA_date_reality' => [
                'sort_field' => DB::raw('CONCAT(orders.ETA_date_reality, "", orders.ETA_time_reality)'),
                'is_sort_raw' => true
            ],
            'name_of_customer_id' => [
                'filter_field' => 'customer.full_name',
            ],
            'name_of_client_id' => [
                'filter_field' => 'client.full_name',
            ],
            'name_of_payment_user_id' => [
                'filter_field' => 'pu.username',
            ],
            'name_of_ins_id' => [
                'filter_field' => 'ai.username'
            ],
            'name_of_upd_id' => [
                'filter_field' => 'au.username',
            ],
            'name_of_vehicle_group_id' => [
                'filter_field' => 'vg.name',
            ],
            'name_of_partner_id' => [
                'filter_field' => 'p.full_name',
            ],
        ];
    }

    /**
     * @param  $columns
     * @return Builder
     */
    public function getQueryBuilder($columns)
    {
        $query = $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('locations as ld', $this->getTableName() . '.location_destination_id', '=', 'ld.id')
            ->leftJoin('locations as la', $this->getTableName() . '.location_arrival_id', '=', 'la.id')
            ->leftJoin('m_province as lpd', 'ld.province_id', '=', 'lpd.province_id')
            ->leftJoin('m_district as ldd', 'ld.district_id', '=', 'ldd.district_id')
            ->leftJoin('m_province as lpa', 'la.province_id', '=', 'lpa.province_id')
            ->leftJoin('m_district as lda', 'la.district_id', '=', 'lda.district_id')
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id')
            ->leftJoin('customer as customer', $this->getTableName() . '.customer_id', '=', 'customer.id')
            ->leftJoin('customer as client', $this->getTableName() . '.client_id', '=', 'client.id')
            ->leftJoin('drivers as d', $this->getTableName() . '.primary_driver_id', '=', 'd.id')
            ->leftJoin('vehicle as v', $this->getTableName() . '.vehicle_id', '=', 'v.id')
            ->leftJoin('m_vehicle_group as vg', 'v.group_id', '=', 'vg.id')
            ->leftJoin('order_payment as op', $this->getTableName() . '.id', '=', 'op.order_id')
            ->leftJoin('admin_users as pu', 'op.payment_user_id', '=', 'pu.id')
            ->leftJoin('partner as p', 'p.id', '=', $this->getTableName() . '.partner_id');

        /* if ($this->_filterCustomerRole && !empty(Auth::User())) {

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
             $query->whereIn($this->getTableName() . '.customer_id', $customerIDs);
         }*/

        $query = $query->orderBy($this->getSortField(), $this->getSortType());
        return $query;
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        if (!isset($query['sort_field'])) {
            $query['sort_field'] = 'id';
            $query['sort_type'] = 'asc';
        }
        $columns = [
            '*',
            'customer.customer_code as code_of_customer_code',
            'customer.full_name as name_of_customer_code',

            'v.reg_no as code_of_vehicle',

            'd.code as code_of_primary_driver',
            'd.full_name as name_of_primary_driver',

            'pu.username as code_of_payment_user_id',
            'pu.full_name as name_of_payment_user_id',

            'op.payment_type',
            'op.goods_amount',
            'op.vat',
            'op.anonymous_amount',

            'lpd.title as name_of_province_destination_id',
            'lpa.title as name_of_province_arrival_id',
            'ldd.title as name_of_district_destination_id',
            'lda.title as name_of_district_arrival_id',

        ];
        $this->_filterCustomerRole = true;
        $query = $this->handleStatusSearch($query);
        $queryBuilder = $this->search($query, $columns)->with(["listGoods"]);
        return $this->_withRelations($queryBuilder)->paginate($limit, ['*'], 'page', 1);
    }

    public function getOrderByID($id, $driverId = 0)
    {
        $ordersQuery = DB::table('orders')
            ->leftJoin('drivers', 'drivers.id', '=', 'orders.primary_driver_id')
            ->leftJoin('vehicle', 'orders.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('customer', 'orders.customer_id', '=', 'customer.id')
            ->leftJoin(
                'locations as location_destination',
                'location_destination.id',
                '=',
                'orders.location_destination_id'
            )
            ->leftJoin(
                'locations as location_arrival',
                'location_arrival.id',
                '=',
                'orders.location_arrival_id'
            )
            ->where([
                ['orders.id', '=', $id],
                ['orders.del_flag', '=', '0'],
                ['customer.del_flag', '=', '0']
            ]);
        if ($driverId != 0) {
            $ordersQuery->where('orders.primary_driver_id', '=', $driverId);
        }
        $order = $ordersQuery->get([
            'orders.*',
            'orders.id as order_id',
            'customer.full_name as customer_full_name',
            DB::raw('CASE WHEN location_destination.full_address IS NULL OR location_destination.full_address = "" THEN location_destination.title ELSE location_destination.full_address END as location_destination'),
            DB::raw('CASE WHEN location_arrival.full_address IS NULL OR location_arrival.full_address = "" THEN location_arrival.title ELSE location_arrival.full_address END as location_arrival'),
            'location_destination.latitude as location_des_lat',
            'location_destination.longitude as location_des_long',
            'location_arrival.latitude as location_arr_lat',
            'location_arrival.longitude as location_arr_long',
            'drivers.full_name',
            'vehicle.reg_no'
        ])->first();

        if ($order != null) {
            $locationQuery = DB::table('order_locations')
                ->join('locations', 'order_locations.location_id', '=', 'locations.id')
                ->where('order_locations.order_id', '=', $id);
            $order->listLocations = $locationQuery->get(['order_locations.location_id', 'order_locations.date', 'order_locations.time', 'order_locations.type', 'locations.title', 'locations.full_address']);

            $listGoodsQuery = DB::table('order_goods')
                ->join('goods_type', 'order_goods.goods_type_id', '=', 'goods_type.id')
                ->leftJoin('goods_unit', 'order_goods.goods_unit_id', '=', 'goods_unit.id')
                ->where('order_goods.order_id', '=', $id);
            $order->listGoods = $listGoodsQuery
                ->select([
                    'order_goods.quantity',
                    'order_goods.goods_type_id',
                    'order_goods.goods_unit_id',
                    'order_goods.quantity',
                    'order_goods.insured_goods',
                    'order_goods.note',
                    'order_goods.weight',
                    'order_goods.volume',
                    'goods_unit.title as unitTitle',
                    'goods_type.title'
                ])
                ->get();

            $order->histories = DB::table('order_history')
                ->where('order_id', '=', $id)
                ->get();
            $order->app_url = env('APP_URL');
        }


        return $order;
    }

    // Lay thong tin dia diem, trang thai order
    public function getOrderInfoById($orderId)
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
                ['orders.id', '=', $orderId],
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

    public function getVehicleAndDriverForOrder($orderId)
    {
        if ($orderId == null)
            return null;
        $ordersQuery = DB::table('orders')
            ->leftJoin('vehicle', 'vehicle.id', '=', 'orders.vehicle_id')
            ->leftJoin('drivers', 'drivers.id', '=', 'orders.primary_driver_id')
            ->where([
                ['orders.id', '=', $orderId],
                ['orders.del_flag', '=', '0'],
                ['vehicle.del_flag', '=', '0'],
                ['drivers.del_flag', '=', '0'],
            ]);
        return $ordersQuery->get([
            'orders.vehicle_id',
            'orders.primary_driver_id as driver_id',
            'vehicle.reg_no',
            'drivers.full_name',
            'vehicle.group_id'
        ])->first();
    }

    public function getVehicleForOrders($orderIds)
    {
        if (empty($orderIds))
            return [];
        $ordersQuery = DB::table('orders')
            ->leftJoin('vehicle', 'vehicle.id', '=', 'orders.vehicle_id')
            ->leftJoin('drivers', 'drivers.id', '=', 'orders.primary_driver_id')
            ->whereIn('orders.id', $orderIds)
            ->where([
                ['orders.del_flag', '=', '0'],
                ['vehicle.del_flag', '=', '0'],
            ]);
        return $ordersQuery->select([
            'orders.vehicle_id',
            'vehicle.reg_no',
            'vehicle.group_id'
        ])->distinct()->get();
    }

    /**
     * @param $data
     * @param Order $entity
     * @return mixed
     */
    public function processExtendData($data, $entity)
    {
        if (empty($data)) {
            return $entity;
        }

        $extendData = [];

        $extendData['route_id'] = isset($data['route_id']) ? $data['route_id'] : 0;
        $extendData['current_route_id'] = isset($data['current_route_id']) ? $data['current_route_id'] : 0;
        $extendData['vehicle_id'] = isset($data['vehicle_id']) ? $data['vehicle_id'] : 0;
        $extendData['current_vehicle_id'] = isset($data['current_vehicle_id']) ? $data['current_vehicle_id'] : 0;
        $extendData['primary_driver_id'] = isset($data['primary_driver_id']) ? $data['primary_driver_id'] : 0;
        $extendData['current_primary_driver_id'] = isset($data['current_primary_driver_id']) ? $data['current_primary_driver_id'] : 0;
        $extendData['secondary_driver_id'] = isset($data['secondary_driver_id']) ? $data['secondary_driver_id'] : 0;
        $extendData['current_secondary_driver_id'] = isset($data['current_secondary_driver_id']) ? $data['current_secondary_driver_id'] : 0;

        $entity->setExtendData($extendData);
        return $entity;
    }

    public function getOrderByFilterReport($filter)
    {
        return $this->getOrderByReportFilter($filter);
    }

    public function getLocationItemsByOrderID($orderId)
    {
        if (empty($orderId)) {
            return [];
        }
        $query = DB::table('order_locations')
            ->leftJoin('locations', 'order_locations.location_id', '=', 'locations.id')
            ->where([
                ['order_locations.order_id', '=', $orderId]
            ])
            ->orderBy('order_locations.date')
            ->orderBy('order_locations.time');
        return $query->get([
            'order_locations.date',
            'order_locations.time',
            'order_locations.type',
            'order_locations.note',
            'locations.full_address',
            'locations.id',
            DB::raw('(CASE WHEN locations.address is null or locations.address = "" THEN locations.title ELSE locations.address END) as address'),
            'locations.longitude',
            'locations.latitude'
        ]);
    }

    public function getGoodsItemsByOrderID($orderId)
    {
        if (empty($orderId)) {
            return [];
        }
        $query = DB::table('order_goods')
            ->leftJoin('goods_type', 'order_goods.goods_type_id', '=', 'goods_type.id')
            ->leftJoin('goods_unit', 'order_goods.goods_unit_id', '=', 'goods_unit.id')
            ->where([
                ['order_goods.order_id', '=', $orderId],
                ['order_goods.del_flag', '=', 0],
            ]);
        return $query->get([
            'order_goods.id',
            'order_goods.quantity',
            'order_goods.insured_goods',
            'order_goods.note',
            'order_goods.weight',
            'order_goods.volume',
            'order_goods.total_weight',
            'order_goods.total_volume',
            'goods_type.title as good_type_title',
            'goods_unit.title as good_unit_title',
            'goods_unit.id as goods_unit_id',
            'goods_type.id as good_type_id',
            'goods_type.id as goods_type_id'
        ]);
    }

    public function updateOrderLocation($orderId, $locationId, $type, $date, $time)
    {
        if ($orderId == null || $locationId == null)
            return null;

        return DB::table('order_locations')
            ->where([
                ['order_locations.order_id', '=', $orderId],
                ['order_locations.location_id', '=', $locationId],
                ['order_locations.type', '=', $type]
            ])->update(['date' => $date, 'time' => $time]);
    }

    public function updateOrderPayment($orderId, $paymentType, $paymentUserId, $goodsAmount, $vat, $anonymousAmount)
    {
        if ($orderId == null)
            return null;

        return DB::table('order_payment')
            ->where([
                ['order_payment.order_id', '=', $orderId],
            ])->update([
                'payment_type' => $paymentType, 'payment_user_id' => $paymentUserId,
                'goods_amount' => $goodsAmount, 'vat' => $vat, 'anonymous_amount' => $anonymousAmount
            ]);
    }

    public function getVehicleTeamFromDriverId($driverId)
    {
        $query = "SELECT GROUP_CONCAT(`vehicle_team`.name SEPARATOR '|| ') as vehicle_team_name
                    FROM `driver_vehicle_team`
                      LEFT JOIN `vehicle_team` ON `vehicle_team`.`id` = `driver_vehicle_team`.`vehicle_team_id`
                    WHERE (`driver_vehicle_team`.`driver_id` = " . $driverId . ")";
        return DB::select($query);
    }

    public function getOrderDocumentByCode($code, $orderNo)
    {
        if (empty($code) && empty($orderNo)) {
            return null;
        }
        $this->_filterCustomerRole = false;
        if (!empty($code)) {
            return $this->search([
                'order_code_eq' => $code
            ], [])->get();
        } else {
            return $this->search([
                'order_no_eq' => $orderNo
            ], [])->get();
        }
    }


    // Lấy dữ liệu Xe để xuất theo mẫu
    // CreatedBy nlhoang 05/04/2020
    public function getExportByID($id, $template)
    {
        return $this->getDataForTemplateByID($id, $template);
    }

    public function updateDateRealityOrderLocation($orderId, $locationId, $dateReality, $timeReality)
    {
        DB::table('order_locations')->where([
            ['order_locations.order_id', '=', $orderId],
            ['order_locations.location_id', '=', $locationId]
        ])->update(['date_reality' => $dateReality, 'time_reality' => $timeReality]);
    }

    public function updateDateTimeCollectedDocument($orderId, $ETA_date_reality = null, $ETA_time_reality = null)
    {
        $query = DB::table('orders')
            ->leftJoin('locations', 'orders.location_arrival_id', '=', 'locations.id')
            ->where('orders.id', '=', $orderId)
            ->select(['locations.limited_day', 'orders.ETA_date_reality', 'orders.ETA_time_reality'])->first();

        if ($ETA_date_reality == null && $ETA_time_reality == null && $query) {
            $ETA_date_reality = $query->ETA_date_reality;
            $ETA_time_reality = $query->ETA_time_reality;
        }

        if ($query && $query->limited_day && $query->limited_day != 0 && $ETA_date_reality && $ETA_time_reality) {
            $dateCollected = date('Y-m-d', strtotime($ETA_date_reality . ' + ' . (int)$query->limited_day . ' days'));
            $timeCollected = $ETA_time_reality;
            DB::table('orders')->whereNull('orders.date_collected_documents')
                ->where('orders.id', '=', $orderId)
                ->update(['date_collected_documents' => $dateCollected, 'time_collected_documents' => $timeCollected]);
        }
    }

    public function getOrdersByOrderNo($orderNo)
    {
        if (empty($orderNo)) {
            return null;
        }
        $this->_filterCustomerRole = false;

        return $this->search([
            'order_no_eq' => $orderNo
        ], [])->get();
    }

    public function checkRoleOrder($id)
    {
        if (!$id)
            return false;
        $orders = DB::table('orders')
            ->leftJoin('customer_group_customer as cgc', function ($join) {
                $join->on('cgc.customer_id', '=', 'orders.customer_id')
                    ->where('cgc.del_flag', '=', 0);
            })
            ->leftJoin('admin_users_customer_group as aucg', function ($join) {
                $join->on('aucg.customer_group_id', '=', 'cgc.customer_group_id')
                    ->where('aucg.del_flag', '=', 0);
            })
            ->where('orders.id', '=', $id)
            ->where(function ($query) {
                $query->where('aucg.admin_user_id', '=', Auth::User()->id)
                    ->orWhereNull('aucg.customer_group_id');
            })->get();
        if ($orders && count($orders) > 0)
            return true;

        return false;
    }


    // Lấy danh sách đơn hàng để tự động tính giá
    // CreatedBy nlhoang 03/08/2020
    public function getOrdersToCalcPrice($param)
    {
        $dayCondition = $param["day_condition"];
        $fromDate = $param["from_date"];
        $toDate = $param["to_date"];

        switch ($dayCondition) {
            case 1:
                $paramDayCondition = 'o.ETD_date';
                break;
            case 2:
                $paramDayCondition = 'o.ETD_date_reality';
                break;
            case 3:
                $paramDayCondition = 'o.ETA_date';
                break;
            case 4:
                $paramDayCondition = 'o.ETA_date_reality';
                break;
        }
        $orderNotices = DB::table('orders as o')
            ->leftJoin('order_price as op', 'op.order_id', '=', 'o.id')
            ->where([
                ['o.del_flag', '=', 0],
                ['o.amount', '=', 0],
            ])
            ->where(function ($query) {
                $query->where('op.is_approved', '!=', 1)
                    ->orWhereNull('op.order_id');
            })
            ->whereDate($paramDayCondition, '>=', $fromDate)
            ->whereDate($paramDayCondition, '<=', $toDate)
            ->get([
                'o.id as order_id',
                'o.customer_id as customer_id',

            ]);

        return $orderNotices;
    }

    // Lấy chi tiết của đơn hàng để tính giá
    // CreatedBy nlhoang 03/08/2020
    public function getOrderDetailToCalcPrice($id, $is_include_goods = false)
    {
        $orderDetail = DB::table('orders as o')
            ->leftJoin('locations as l1', 'l1.id', '=', 'o.location_destination_id')
            ->leftJoin('locations as l2', 'l2.id', '=', 'o.location_arrival_id')
            ->leftJoin('location_group as lg1', 'lg1.id', '=', 'l1.location_group_id')
            ->leftJoin('location_group as lg2', 'lg2.id', '=', 'l2.location_group_id')
            ->leftJoin('vehicle', 'vehicle.id', '=', 'o.vehicle_id')
            ->leftJoin('m_vehicle_group as vg', 'vg.id', '=', 'vehicle.group_id')
            ->leftJoin('customer as c', 'c.id', '=', 'o.customer_id')
            ->where([
                ['o.id', '=', $id],
            ])
            ->select([
                'o.id as order_id',
                'o.customer_id as customer_id',
                'l1.location_group_id as location_group_destination_id',
                'l2.location_group_id as location_group_arrival_id',
                'vehicle.group_id as vehicle_group_id',
                'o.volume',
                'o.weight',
                DB::raw('IFNULL(o.gps_distance, 0) as distance')

            ])->first();
        if ($is_include_goods == true) {
            $goodsItems =
                DB::table('order_goods as og')
                    ->leftJoin('goods_type as gt', 'gt.id', '=', 'og.goods_type_id')
                    ->where('og.order_id', $id)
                    ->get([
                        'og.order_id as order_id',
                        'og.quantity',
                        'og.goods_type_id as goods_type_id',
                        'gt.title as name_of_goods_type_id'
                    ]);

            $orderDetail->{'goods'} = $goodsItems;
        }
        return $orderDetail;
    }

    // Lấy tài xế và xe mặc định theo danh sách đơn hàng
    public function getDefaultVehicleAndDriverByOrderIDs($orderIds)
    {
        if (empty($orderIds))
            return [];
        $ordersQuery = DB::table('orders')
            ->leftJoin('vehicle', 'vehicle.id', '=', 'orders.vehicle_id')
            ->whereIn('orders.id', $orderIds)
            ->where([
                ['orders.del_flag', '=', '0'],
            ]);
        $totalQuery = clone $ordersQuery;
        $total = $totalQuery->select([
            DB::raw("MAX(CONCAT(ETA_date, ' ', ETA_time)) as time"),
            DB::raw("SUM(orders.weight) as total_weight"),
            DB::raw("SUM(orders.volume) as total_volume")
        ])->groupBy('orders.id')->first();
        $vehicle = $ordersQuery->select(['vehicle.id', 'vehicle.reg_no'])->distinct()->first();

        if (!empty($vehicle)) {
            $id = $vehicle->id;
            $driver = DB::table('driver_vehicle')
                ->join('drivers', 'drivers.id', '=', 'driver_vehicle.driver_id')
                ->where('vehicle_id', '=', $id)
                ->orderBy('driver_vehicle.ins_date', 'desc')
                ->select(['drivers.id', 'drivers.full_name'])
                ->first();

            $partner = DB::table('partner')
                ->join('vehicle', 'vehicle.partner_id', '=', 'partner.id')
                ->where('vehicle.id', '=', $id)
                ->orderBy('partner.ins_date', 'desc')
                ->select(['partner.id', 'partner.full_name'])
                ->first();
        }

        $result = [
            'info' => [
                'time' => !empty($total) ? $total->time : 0,
            ],
            'goods' => [
                'total_weight' => $total && $total->total_weight ? $total->total_weight : 0,
                'total_volume' => $total && $total->total_volume ? $total->total_volume : 0,
            ],
            'vehicle' => [
                'id' => !empty($vehicle) ? $vehicle->id : null,
                'title' => !empty($vehicle) ? $vehicle->reg_no : null
            ],
            'driver' => [
                'id' => !empty($driver) ? $driver->id : null,
                'title' => !empty($driver) ? $driver->full_name : null
            ],
            'partner' => [
                'id' => !empty($partner) ? $partner->id : null,
                'title' => !empty($partner) ? $partner->full_name : null
            ]
        ];
        return $result;
    }

    //Lấy đơn theo chuyến
    public function getOrdersByRouteId($routeId)
    {
        if (!$routeId) {
            return null;
        }
        $this->_filterCustomerRole = false;
        return $this->search(['route_id_eq' => $routeId])->get([
            '*',
            DB::raw('"" as goods_type')
        ]);
    }

    //Lấy đơn theo DHKH
    public function getOrdersByOrderCustomerId($orderCustomerId)
    {
        if (!$orderCustomerId) {
            return null;
        }
        $this->_filterCustomerRole = false;
        return $this->search(['order_customer_id_eq' => $orderCustomerId])->orderBy('id', 'asc')->get();
    }

    //Lấy đơn theo chuyến, tài xế hoặc xe
    public function getItemsByRouteIDOrVehicleIDOrDriverID($routeId, $vehicleId, $driverId)
    {
        $partnerId = Auth::user()->partner_id;

        //Lấy đơn hàng chưa thuộc chuyến nào và đơn hàng đang thuộc chuyến
        $query = DB::table('orders as o')
            ->select("o.id", "o.order_code as title", "o.customer_name")
            ->leftJoin('customer_group_customer as cgc', function ($join) {
                $join->on('cgc.customer_id', '=', 'o.customer_id')
                    ->where('cgc.del_flag', '=', 0);
            })->leftJoin('admin_users_customer_group as aucg', function ($join) {
                $join->on('aucg.customer_group_id', '=', 'cgc.customer_group_id')
                    ->where('aucg.del_flag', '=', 0);
            })
            ->where('o.order_code', 'LIKE', '%' . request('q') . '%')
            ->where('o.del_flag', '=', 0)
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
            });
        if ($partnerId) {
            $query = $query->where('o.partner_id', '=', $partnerId);
        }
        $query = $query->distinct()
            ->orderBy('o.upd_date', 'desc')
            ->paginate(10);
        return $query;
    }

    //Lấy đơn theo chuyến, tài xế hoặc xe
    public function getExpiredItem()
    {
        $currentDate = date('Y-m-d');
        $expiredDate = date('Y-m-d', strtotime($currentDate . ' + 1 days'));
        $currentTime = date('H:m');

        $query = DB::table("orders")
            ->where('orders.del_flag', '=', 0)
            ->where('orders.status', '=', config("constant.SAN_SANG"))
            ->where('ETD_date', '>=', $currentDate)
            ->where('ETD_date', '<=', $expiredDate)
            ->where('ETD_time', '>=', $currentTime);
        $orders = $query->get();

        return $orders;
    }

    public function getModelById($id)
    {
        if ($id == null) {
            return [];
        }
        return Order::find($id);
    }

    public function getItemsByOrderNo($q, $orderNo)
    {
        //Lấy đơn hàng có cùng số đơn hàng
        $query = DB::table('orders as o')
            ->select("o.id", "o.order_code as title", "o.order_no", "o.customer_name")
            ->where('o.order_code', 'LIKE', '%' . $q . '%')
            ->where('o.order_no', 'LIKE', '%' . $orderNo . '%')
            ->where('o.del_flag', '=', 0)
            ->distinct()
            ->orderBy('o.upd_date', 'desc')
            ->paginate(10);
        return $query;
    }

    //Tạo câu lệnh lấy ds đơn hàng trên BDK
    public function buildQueryForBoard($params)
    {
        $partnerId = Auth::user()->partner_id;
        $vehiclePageIndex = $params['vehiclePageIndex'];
        $vehiclePageSize = $params['vehiclePageSize'];
        $vehicleTeamIDs = $params['vehicleTeamIDs'];
        $vehicleGroupIDs = $params['vehicleGroupIDs'];
        $vehicleIDs = $params['vehicleIDs'];
        $customerIDs = $params['customerIDs'];
        $originalStatus = $params['originalStatus'];
        $statuses = $params['statuses'];

        $isShowCustomer = $params['isShowCustomer'];
        $start = $params['start'];
        $end = $params['end'];
        $userId = $params['userId'];

        $statuses = implode(',', $statuses);
        $offset = ($vehiclePageIndex - 1) * $vehiclePageSize;

        $vehicleQuery = DB::table('vehicle as v')
            ->where('v.active', '=', 1)
            ->where('v.status', '!=', 4)
            ->where('v.del_flag', '=', 0);

        $vehicleQuery = $vehicleQuery
            ->leftJoin('driver_vehicle as dv', 'v.id', '=', 'dv.vehicle_id')
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
            'o.id AS id',
            DB::raw('exists (select 1 from order_file where order_file.order_id = o.id and order_file.file_id is not null and order_file.file_id != "" and order_file.del_flag = 0 limit 1) as is_attachment'),
            'o.vehicle_id AS resourceId',
            'o.order_code AS title',
            'o.status AS status',
            'o.id AS orderId',
            DB::raw('CONCAT(o.ETD_date_reality,
                            " ",
                            o.ETD_time_reality) AS real_start'),
            DB::raw('CONCAT(o.ETA_date_reality,
                            " ",
                            o.ETA_time_reality) AS real_end'),
            DB::raw('CONCAT(o.ETA_date, " ", o.ETA_time) AS end'),
            DB::raw('CONCAT(o.ETD_date, " ", o.ETD_time) AS start'),
            DB::raw('CASE WHEN (o.status = 2) THEN
                           "#67d1f8"
                         WHEN (o.status = 3) THEN
                           "#9d5508"
                         WHEN(o.status = 4) THEN
                           "#678bfb"
                        WHEN(o.status = 5) THEN
                           "#28a745"
                         WHEN(o.status = 6) THEN
                           "#1d2124"
                         WHEN (o.status = 7) THEN
                           "#aa315b"
                         else "" END color')
        ];

        $orderQuery = DB::table('orders as o')
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
            $orderQuery->whereIn('o.status', explode(',', $statuses));
        }
        if (!empty($customerIDs)) {
            $orderQuery->whereIn('o.customer_id', explode(',', $customerIDs));
        }
        if ($partnerId) {
            $orderQuery->where('o.partner_id', '=', $partnerId);
        }
        if ($isShowCustomer != 1) {
            $cIDs = DB::table('customer AS t1')
                ->leftJoin('customer_group_customer AS t2', 't2.customer_id', '=', 't1.id')
                ->leftJoin('admin_users_customer_group AS t3', 't3.customer_group_id', '=', 't2.customer_group_id')
                ->where('t1.del_flag', '=', 0)
                ->where(function ($query) use ($userId) {
                    $query->where(function ($q) use ($userId) {
                        $q->where('t3.admin_user_id', '=', $userId)
                            ->where('t3.del_flag', '=', 0);
                    })
                        ->orWhereNull('t2.customer_id');
                })
                ->groupBy('t1.id')->pluck('t1.id as customer_id')->toArray();
            $nullCustomerID = 0;
            array_push($cIDs, $nullCustomerID);
            $orderQuery->whereIn('o.customer_id', $cIDs);
        }
        $groupQuery = clone $orderQuery;

        $result = $orderQuery->whereIn('o.vehicle_id', $vIDs)->select($columns)->get();
        $group = $groupQuery->whereIn('o.vehicle_id', $totalVehicleIDs)->groupBy('o.status')
            ->select([
                'o.status',
                DB::raw('COUNT(o.id) as total')
            ])->get();

        if ($originalStatus != -1) {
            $vIDs = $result->unique('resourceId')->pluck('resourceId')->toArray();
            $totalResource = count($vIDs);
        }
        return [
            'totalResource' => $totalResource,
            'resourceIDs' => $vIDs,
            'result' => $result,
            'group' => $group
        ];
    }

    // Khoá đơn hàng
    //CreatedBy nlhoang 29/09/2020
    public function lock($type, $fromDate, $toDate)
    {
        $this->_doLock('lock', $type, $fromDate, $toDate);
    }

    // Mở khoá đơn hàng
    //CreatedBy nlhoang 29/09/2020
    public function unlock($type, $fromDate, $toDate)
    {
        $this->_doLock('unlock', $type, $fromDate, $toDate);
    }

    private function _doLock($lockType, $type, $fromDate, $toDate)
    {
        $lockType = $lockType == 'lock' ? 1 : 0;
        $query = Order::where('del_flag', '=', 0);
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

    public function getRouteLocations($routeId)
    {
        if ($routeId == null)
            return null;

        $this->_filterCustomerRole = false;
        $locations = $this->search([
            'route_id_eq' => $routeId
        ], [
            'ld.id as destination_location_id', 'ld.title as destination_location_title', 'ETD_date as destination_location_date', 'ETD_time as destination_location_time',
            'la.id as arrival_location_id', 'la.title as arrival_location_title', 'ETA_date as arrival_location_date', 'ETA_time as arrival_location_time', 'id as order_id', 'order_code'
        ])->get();
        return $locations;
    }

    public function getOrdersByRouteIds($routeIds)
    {
        if (empty($routeIds)) {
            return [];
        }

        $this->_filterCustomerRole = false;
        $orders = $this->search([
            'route_id_in' => $routeIds
        ])->get();

        return $orders;
    }

    public function validHasPartnerAccept($orderIds)
    {
        if (empty($orderIds)) {
            return false;
        }
        $orders = DB::table('orders')
            ->whereIN('id', $orderIds)
            ->whereNotNull('partner_id')
            ->where('partner_id', '<>', 0)
            ->whereIn('status', [
                config('constant.SAN_SANG'), config('constant.CHO_NHAN_HANG'),
                config('constant.DANG_VAN_CHUYEN'), config('constant.HOAN_THANH'), config('constant.TAI_XE_XAC_NHAN')
            ])
            ->get();
        if ($orders && count($orders) > 0)
            return true;

        return false;
    }

    public function getOrderGoodsByOrderIds($orderIds)
    {
        if (empty($orderIds)) {
            return false;
        }
        $orderGoods = OrderGoods::whereIN('order_id', $orderIds)
            ->where('del_flag', '=', 0)
            ->get();

        return $orderGoods;
    }

    public function validHasRoute($orderIds)
    {
        if (empty($orderIds)) {
            return false;
        }
        $orders = DB::table('orders')
            ->whereIN('id', $orderIds)
            ->whereNull('route_id')
            ->get();
        if ($orders && count($orders) > 0)
            return false;

        return true;
    }

    public function _hasDelete($id)
    {
        $userId = Auth::user()->id;

        $order = $this->search(['id_eq', $id])->first();
        if (!$order)
            return false;

        //Nếu đơn ko ở trạng thái khởi tạo
        if ($order->status != config('constant.KHOI_TAO'))
            return false;

        //Nếu đơn ko phải do user tạo
        if ($order->ins_id != $userId)
            return false;

        return true;
    }

    public function validMatchOrderNo($orderIds)
    {
        if (empty($orderIds)) {
            return false;
        }
        $orders = DB::table('orders')
            ->whereIN('id', $orderIds)
            ->groupBy('order_no')
            ->get();
        if ($orders && count($orders) > 1)
            return false;

        return true;
    }

    public function validHasOrderCancel($orderIds)
    {
        if (empty($orderIds)) {
            return false;
        }
        $orders = DB::table('orders')
            ->whereIN('id', $orderIds)
            ->where(function ($query) {
                return $query->where('status', '=', config('constant.HUY'))
                    ->orWhere('status_partner', '=', config('constant.PARTNER_HUY'));
            })
            ->get();
        if ($orders && count($orders) > 0)
            return true;

        return false;
    }

    public function getQueryGroup($query)
    {
        $tableName = $this->getTableName();
        $query = $this->handleStatusSearch($query);
        $queryBuilder = $this->search($query, []);
        $queryBuilder->getQuery()->orders = null;
        return $queryBuilder
            ->select([$tableName . '.status', $tableName . '.status_partner', DB::raw('count(*) as total')])
            ->groupBy([$tableName . '.status', $tableName . '.status_partner'])
            ->get();
    }
}
