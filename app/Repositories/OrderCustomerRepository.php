<?php

namespace App\Repositories;

use App\Model\Entities\Order;
use App\Model\Entities\OrderCustomer;
use App\Repositories\Traits\OrderCustomerExportTrait;
use App\Repositories\Base\CustomRepository;
use App\Validators\OrderCustomerValidator;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderCustomerRepository extends CustomRepository
{
    use OrderCustomerExportTrait;

    protected $_fieldsSearch = ['name', 'code', 'order_date', 'ETD_date', 'ETA_date', 'ETD_date_reality', 'ETA_date_reality'];
    protected $_filterCustomerRole = false;
    protected $_filterBackend = true;

    function model()
    {
        return OrderCustomer::class;
    }

    public function getGroupColumn()
    {
        return 'status';
    }

    public function validator()
    {
        return OrderCustomerValidator::class;
    }

    protected function _withRelations($query)
    {
        return $query->with([]);
    }

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

    public function getItemByOrderNo($orderNo)
    {
        if (empty($orderNo)) {
            return null;
        }
        return $this->search([
            'order_no_eq' => $orderNo
        ])->first();
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $userId = Auth::user()->id;
        $columns = [
            '*',
            'c.full_name as name_of_customer_id',
            'cl.full_name as name_of_client_id',
            'ld.title as name_of_location_destination_id',
            'la.title as name_of_location_arrival_id',
            'pu.username as pu_username',
            'lpd.title as name_of_province_destination_id',
            'lpa.title as name_of_province_arrival_id',
            'ldd.title as name_of_district_destination_id',
            'lda.title as name_of_district_arrival_id',
            DB::raw('case when order_customer.ins_id != ' . $userId . ' or order_customer.status in ('
                . config('constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN') . ',' . config('constant.ORDER_CUSTOMER_STATUS.HOAN_THANH')
                . ')  then 0 else 1 end as is_action')
        ];
        $this->_filterCustomerRole = true;
        $queryBuilder = $this->search($query, $columns)->with(['insUser', 'updUser']);
        return $queryBuilder->paginate($perPage);
    }

    //Lấy ds DHKH vãng lai
    public function getListForClient($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
            'c.full_name as name_of_customer_id',
            'ld.title as name_of_location_destination_id',
            'la.title as name_of_location_arrival_id'
        ];
        $this->_filterCustomerRole = true;
        $this->_filterBackend = false;
        $queryBuilder = $this->search($query, $columns);
        return $queryBuilder->paginate($perPage);
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        $columns = [
            '*',
            'c.customer_code as code_of_customer_code',
            'cl.full_name as name_of_customer_code',
            'ld.code as code_of_location_destination_code',
            'ld.title as name_of_location_destination_code',
            'la.code as code_of_location_arrival_code',
            'la.title as name_of_location_arrival_code',
            'pu.username as code_of_payment_user_id',
            'pu.full_name as name_of_payment_user_id',
        ];
        $this->_filterCustomerRole = true;
        $queryBuilder = $this->search($query, $columns);
        $queryBuilder->with(['listVehicleGroups']);
        return $this->_withRelations($queryBuilder)->paginate($limit, ['*'], 'page', 1);
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 31/08/2020
    protected function getKeyValue()
    {
        return [
            'name_of_customer_id' => [
                'filter_field' => 'c.full_name',
            ],
            'name_of_client_id' => [
                'filter_field' => 'cl.full_name',
            ],
            'name_of_location_destination_id' => [
                'filter_field' => 'ld.title',
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
            'name_of_location_arrival_id' => [
                'filter_field' => 'la.title',
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
            'ETD_date' => [
                'sort_field' => DB::raw('CONCAT(' . $this->getTableName() . '.ETD_date, "", ' . $this->getTableName() . '.ETD_time)'),
                'is_sort_raw' => true
            ],
            'ETA_date' => [
                'sort_field' => DB::raw('CONCAT(' . $this->getTableName() . '.ETA_date, "", ' . $this->getTableName() . '.ETA_time)'),
                'is_sort_raw' => true
            ],
            'ETD_date_reality' => [
                'sort_field' => DB::raw('CONCAT(' . $this->getTableName() . '.ETD_date_reality, "", ' . $this->getTableName() . '.ETD_time_reality)'),
                'is_sort_raw' => true
            ],
            'ETA_date_reality' => [
                'sort_field' => DB::raw('CONCAT(' . $this->getTableName() . '.ETA_date_reality, "", ' . $this->getTableName() . '.ETA_time_reality)'),
                'is_sort_raw' => true
            ],
        ];
    }

    // Hàm build câu lệnh đơn hàng khách hàng
    // CreatedBy nlhoang 31/08/2020
    protected function getQueryBuilder($columns)
    {
        $q = $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('customer as c', $this->getTableName() . '.customer_id', '=', 'c.id')
            ->leftJoin('customer as cl', $this->getTableName() . '.client_id', '=', 'cl.id')
            ->leftJoin('locations as ld', $this->getTableName() . '.location_destination_id', '=', 'ld.id')
            ->leftJoin('locations as la', $this->getTableName() . '.location_arrival_id', '=', 'la.id')
            ->leftJoin('m_province as lpd', 'ld.province_id', '=', 'lpd.province_id')
            ->leftJoin('m_district as ldd', 'ld.district_id', '=', 'ldd.district_id')
            ->leftJoin('m_province as lpa', 'la.province_id', '=', 'lpa.province_id')
            ->leftJoin('m_district as lda', 'la.district_id', '=', 'lda.district_id')
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id')
            ->leftJoin('admin_users as pu', $this->getTableName() . '.payment_user_id', '=', 'pu.id');

        /*if ($this->_filterBackend) {
            $q->where(function ($q) {
                $q->where($this->getTableName() . '.source_creation', '=', config('constant.FROM_ADMIN'))
                    ->orWhere($this->getTableName() . '.is_approved', '=', config('constant.DA_PHE_DUYET'));
            });
        } else {
            $q->where($this->getTableName() . '.source_creation', '=', config('constant.FROM_CLIENT'))
                ->where($this->getTableName() . '.is_approved', '=', config('constant.CHUA_PHE_DUYET'));
        }*/
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
            $q->whereIn($this->getTableName() . '.customer_id', $customerIDs);
        }*/
        $q = $q->whereIn($this->getTableName() . '.status', [
            config('constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG'),
            config('constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN'), config('constant.ORDER_CUSTOMER_STATUS.HOAN_THANH'),
            config('constant.ORDER_CUSTOMER_STATUS.C20_HUY')
        ])
            ->orderBy($this->getSortField(), $this->getSortType());
        return $q;
    }

    // Lấy đơn hàng KH theo ID đơn hàng
    public function getItemByOrderID($order_id)
    {
        $result = DB::table('order_customer as oc')
            ->leftJoin('orders as o', 'o.order_customer_id', '=', 'oc.id')
            ->where('o.id', '=', $order_id)
            ->select('oc.id', 'oc.name', 'oc.code', 'oc.order_no')->first();
        return $result;
    }

    // Lấy danh sách đơn hàng của ĐHKH
    public function getOrdersByID($id)
    {
        $result = Order::leftJoin('vehicle as v', 'v.id', '=', 'orders.vehicle_id')
            ->leftJoin('partner as p', 'p.id', '=', 'orders.partner_id')
            ->where('orders.order_customer_id', '=', $id)
            ->where('orders.del_flag', '=', 0)
            ->get([
                'orders.*',
                'v.reg_no',
                'p.full_name as partner_name'
            ]);
        return $result;
    }

    public function getOrderCustomerByFilterReport($filter)
    {
        return $this->getDataByReportFilter($filter);
    }

    public function validOrderNo($orderIds)
    {
        if (empty($orderIds)) {
            return [];
        }

        $query = DB::table('orders as o')
            ->select("o.order_no")
            ->whereIn('o.id', $orderIds)
            ->groupBy("o.order_no")
            ->get();

        return $query;
    }

    // Lấy dữ liệu đơn hàng khách hàng để xuất theo mẫu
    // CreatedBy nlhoang 28/07/2020
    // ModifiedBy nlhoang 21/08/2020 bổ sung in danh sách hàng hoá của đơn hàng
    public function getExportByIDs($ids, $template)
    {
        return $this->getDataForTemplateByID($ids, $template);
    }

    //Tạo câu lệnh lấy ds đơn hàng trên BDK
    public function buildQueryForBoard($params)
    {
        $vehiclePageIndex = $params['vehiclePageIndex'];
        $vehiclePageSize = $params['vehiclePageSize'];
        $customerIDs = $params['customerIDs'];
        $orderNo = $params['orderNo'];
        $statuses = $params['statuses'];
        $start = $params['start'];
        $end = $params['end'];
        $userId = $params['userId'];
        $statuses = implode(',', $statuses);
        $offset = ($vehiclePageIndex - 1) * $vehiclePageSize;

        $columns = [
            'oc.id',
            'oc.customer_id AS resourceId',
            'oc.status  as status',
            DB::raw('oc.order_no as title'),
            DB::raw('CONCAT(oc.ETD_date_reality, " ", oc.ETD_time_reality) AS real_start'),
            DB::raw('CONCAT(oc.ETA_date_reality, " ", oc.ETA_time_reality) AS real_end'),
            DB::raw('CONCAT(oc.ETA_date, " ", oc.ETA_time) AS end'),
            DB::raw('CONCAT(oc.ETD_date, " ", oc.ETD_time) AS start'),
            DB::raw('CASE WHEN oc.status = ' . config('constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG') . ' THEN "#9d5508" 
            WHEN oc.status = ' . config('constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN') . ' THEN  "#678BFB" 
            WHEN oc.status = ' . config('constant.ORDER_CUSTOMER_STATUS.HOAN_THANH') . ' THEN "#52bb56"
            WHEN oc.status = ' . config('constant.ORDER_CUSTOMER_STATUS.C20_HUY') . ' THEN "#343a40" END  AS color')
        ];

        $orderQuery = DB::table('order_customer as oc')
            ->where('oc.del_flag', '=', 0)
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->where('oc.ETD_date', '>=', $start)
                        ->where('oc.ETA_date', '<=', $end);
                })->orWhere(function ($q) use ($start, $end) {
                    $q->where('oc.ETD_date', '<=', $start)
                        ->where('oc.ETA_date', '>=', $end);
                })->orWhere(function ($q) use ($start, $end) {
                    $q->where('oc.ETD_date', '<=', $start)
                        ->where('oc.ETA_date', '>=', $start)
                        ->where('oc.ETA_date', '<=', $end);
                })->orWhere(function ($q) use ($start, $end) {
                    $q->where('oc.ETD_date', '>=', $start)
                        ->where('oc.ETD_date', '<=', $end)
                        ->where('oc.ETA_date', '>=', $end);
                });
            })
            ->select($columns);
        if (!empty($statuses)) {
            $orderQuery->whereIn('oc.status', explode(',', $statuses));
        } else {
            $orderQuery->whereIn('oc.status', [
                config('constant.ORDER_CUSTOMER_STATUS.DA_XUAT_HANG'),
                config('constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN'), config('constant.ORDER_CUSTOMER_STATUS.HOAN_THANH'),
                config('constant.ORDER_CUSTOMER_STATUS.C20_HUY')
            ]);
        }

        if (!empty($orderNo)) {
            $orderQuery->where('oc.order_codes', 'like', $orderNo);
        }
        $customerQuery = DB::table('customer AS t1')
            ->leftJoin('customer_group_customer AS t2', 't2.customer_id', '=', 't1.id')
            ->leftJoin('admin_users_customer_group AS t3', 't3.customer_group_id', '=', 't2.customer_group_id')
            ->where('t1.del_flag', '=', 0)
            ->where(function ($query) use ($userId) {
                $query->where(function ($q) use ($userId) {
                    $q->where('t3.admin_user_id', '=', $userId)
                        ->where('t3.del_flag', '=', 0);
                })
                    ->orWhereNull('t2.customer_id');
            });
        if (!empty($customerIDs)) {
            $customerQuery->whereIn('t1.id', explode(',', $customerIDs));
        }
        $totalResource = $customerQuery->count(DB::raw('distinct t1.id'));
        $cIDs = $customerQuery
            ->groupBy('t1.id')
            ->orderBy('t1.full_name')
            ->skip($offset)
            ->take($vehiclePageSize)
            ->pluck('t1.id as customer_id')
            ->toArray();

        $nullCustomerID = 0;
        array_push($cIDs, $nullCustomerID);

        $orderQuery->whereIn('oc.customer_id', $cIDs);

        $groupQuery = clone $orderQuery;

        $result = $orderQuery->select($columns)->get();

        $group = $groupQuery->groupBy('oc.status')
            ->select([
                'oc.status as status',
                DB::raw('COUNT(oc.id) as total')
            ])->get();

        return [
            'totalResource' => $totalResource,
            'resourceIDs' => $cIDs,
            'result' => $result,
            'group' => $group
        ];
    }

    // Khoá ĐHKH
    //CreatedBy nlhoang 29/09/2020
    public function lock($type, $fromDate, $toDate)
    {
        $this->_doLock('lock', $type, $fromDate, $toDate);
    }

    // Mở khoá ĐHKH
    //CreatedBy nlhoang 29/09/2020
    public function unlock($type, $fromDate, $toDate)
    {
        $this->_doLock('unlock', $type, $fromDate, $toDate);
    }

    private function _doLock($lockType, $type, $fromDate, $toDate)
    {
        $lockType = $lockType == 'lock' ? 1 : 0;
        $query = OrderCustomer::where('del_flag', '=', 0);
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

    public function getOrderCustomerByCodes($orderCustomerCodes = [])
    {
        if (empty($orderCustomerCodes)) {
            return [];
        }

        return $this->search(['code_in' => $orderCustomerCodes])->get();
    }

    public function getGroupStatusDataByType($type, $customerID, $request)
    {
        $fromDate = $request['fromDate'];
        $toDate = $request['toDate'];

        $customer_type = $type == 'customer' ? 'customer_id' : 'client_id';
        $query = DB::table('order_customer as oc')
            ->where('oc.del_flag', '=', 0)
            ->where($customer_type, '=', $customerID);

        if (!empty($fromDate)) {
            $query
                ->where('oc.order_date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->where('oc.order_date', '<=', $toDate);
        }
        $query->groupBy('oc.status')
            ->select([
                'oc.status',
                DB::raw('COUNT(*) as count')
            ]);
        return $query->get();
    }

    public function getOrderDataByRangeTime($type, $customerID, $fromDate, $toDate)
    {
        $customer_type = $type == 'customer' ? 'customer_id' : 'client_id';
        $items = DB::table('order_customer as oc')
            ->where('oc.del_flag', '=', 0)
            ->where($customer_type, '=', $customerID)
            ->where('oc.order_date', '>=', $fromDate)
            ->where('oc.order_date', '<=', $toDate)
            ->groupBy('oc.order_date')
            ->select([
                DB::RAW('DAYOFMONTH(oc.order_date) as date'),
                DB::raw('COUNT(*) as count')
            ])
            ->get();

        $results = [];
        $daysInMonth = now()->daysInMonth;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $item = new \stdClass();
            $item->date = $i;
            $item->count = 0;
            $temp = $items->filter(function ($value) use ($i) {
                return $value->date == $i;
            })->first();
            if (!empty($temp)) {
                $item->count = $temp->count;
            }

            $results[] = $item;
        }
        return $results;
    }

    public function getProfitDataByRangeTime($type, $customerID, $fromDate, $toDate)
    {
        $customer_type = $type == 'customer' ? 'customer_id' : 'client_id';
        $items = DB::table('order_customer as oc')
            ->where('oc.del_flag', '=', 0)
            ->where($customer_type, '=', $customerID)
            ->groupBy('oc.status')
            ->select([
                'oc.status',
                DB::raw('COUNT(*) as count')
            ])
            ->get();

        return $items;
    }

    public function _hasDelete($id)
    {
        $userId = Auth::user()->id;

        $orderCustomer = $this->search(['id_eq', $id])->first();
        if (!$orderCustomer)
            return false;

        //Nếu đơn đang vận chuyển , hoàn thành
        if (in_array($orderCustomer->status, [config('constant.ORDER_CUSTOMER_STATUS.DANG_VAN_CHUYEN'), config('constant.ORDER_CUSTOMER_STATUS.HOAN_THANH')]))
            return false;

        //Nếu đơn ko phải do user tạo
        if ($orderCustomer->ins_id != $userId)
            return false;

        return true;
    }
}
