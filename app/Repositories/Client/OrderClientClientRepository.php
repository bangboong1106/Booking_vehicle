<?php

namespace App\Repositories\Client;

use App\Common\AppConstant;

use App\Model\Entities\OrderCustomer;
use App\Model\Entities\OrderCustomerGoods;
use App\Repositories\CustomerRepository;
use App\Model\Entities\OrderCustomerHistory;
use \App\Repositories\OrderCustomerRepository;
use App\Services\NotificationService;
use App\Validators\OrderClientValidator;

use DB;
use Exception;
use Illuminate\Container\Container as Application;

class OrderClientClientRepository extends OrderCustomerRepository
{
    private $_notificationService;
    private $_customerRepository;

    public function __construct(
        Application $app,
        NotificationService $notificationService,
        CustomerRepository $customerRepository
    )
    {
        parent::__construct($app);
        $this->_notificationService = $notificationService;
        $this->_customerRepository = $customerRepository;
    }

    public function validator()
    {
        return OrderClientValidator::class;
    }

    protected function getClientColumns($clientID, $customerID, $table_name)
    {
        $columns = [
            $table_name . '.*',
            $table_name . '.id as key',
            DB::raw('c.full_name as name_of_client_id'),
            DB::raw('ld.title as name_of_location_destination_id'),
            DB::raw('la.title as name_of_location_arrival_id'),
        ];
        return $columns;
    }

    protected function getClientBuilder($clientID, $customerID, $table_name, $columns, $request)
    {
        $query = DB::table($table_name)
            ->leftJoin('customer as c', $table_name . '.client_id', '=', 'c.id')
            ->leftJoin('locations as ld', $table_name . '.location_destination_id', '=', 'ld.id')
            ->leftJoin('locations as la', $table_name . '.location_arrival_id', '=', 'la.id')
            ->where([
                [$table_name . '.del_flag', '=', '0'],

            ])
            ->select($columns);
        if (!empty($clientID)) {
            $query->where($table_name . '.client_id', '=', $clientID);
        }
        return $query;
    }

    protected function getWhereTextSearch($query, $table_name, $textSearch)
    {
        if (!empty($textSearch)) {
            $query->where(function ($query) use ($textSearch, $table_name) {
                $query->where($table_name . '.code', 'like', '%' . $textSearch . '%')
                    ->orWhere($table_name . '.order_no', 'like', '%' . $textSearch . '%');
            });
        }
        return $query;
    }

    protected function getCustomClientBuilder($query, $clientID, $customerID, $table_name, $columns, $request)
    {
        $status = $request['status'];
        if (isset($status) && 0 < sizeof($status)) {
            $query->whereIn($table_name . '.status', $status);
        }
        $fromDate = $request['fromDate'];
        $toDate = $request['toDate'];
        if (!empty($fromDate)) {
            $query->whereDate($table_name . '.ETD_date', '>=', $fromDate);
        }
        if (!empty($toDate)) {
            $query->whereDate($table_name . '.ETA_date', '<=', $toDate);
        }
        return $query;
    }

    public function getDataForClientByID($customerID, $id)
    {
        $table_name = $this->getTableName();
        $item = parent::getDataForClientByID($customerID, $id);

        $item->histories = DB::table($table_name)
            ->leftJoin('order_customer_history as h', 'h.order_customer_id', '=', $table_name . '.id')
            ->where($table_name . '.id', '=', $id)
            ->where('h.del_flag', '=', 0)
            ->get([
                'h.*'
            ]);

        $item->list_goods = DB::table($table_name)
            ->leftJoin('order_customer_goods as g', 'g.order_customer_id', '=', $table_name . '.id')
            ->leftJoin('goods_type as gt', 'gt.id', '=', 'g.goods_type_id')
            ->leftJoin('files', 'files.file_id', '=', 'gt.file_id')
            ->leftJoin('goods_unit as gu', 'gu.id', '=', 'g.goods_unit_id')
            ->where($table_name . '.id', '=', $id)
            ->where('g.del_flag', '=', 0)
            ->where('g.quantity', '!=', 0)
            ->get([
                'g.*',
                'gt.title as name_of_goods_type_id',
                'gu.title as name_of_goods_unit_id',
                'files.file_name',
                'files.file_type',
                'files.path',
                DB::raw('(g.quantity - COALESCE(g.quantity_out, 0)) as quantity_out_export')

            ]);
        foreach ($item->list_goods as $goods) {
            $goods->file_path = AppConstant::getImagePath($goods->path, $goods->file_type);
        }
        return $item;
    }

    public function deleteDataByID($id)
    {
        $item = OrderCustomer::whereIn('id', $id);
        if (!is_null($item)) {
            $item->delete();
        } else {
            new Exception('Invalid item id: ' . $id);
        }
    }

    public function preSaveEntity($entity, $parameters)
    {
        if (!isset($parameters['id']) || empty($parameters['id'])) {
            $code = app('App\Http\Controllers\Backend\SystemCodeConfigController')->generateSystemCode(config('constant.sc_order_customer'), null, true);
            $entity->code = $entity->order_no = $code;
            $this->_beforeStatus = 1;
            $entity->source_creation = config('constant.SOURCE_CREATE_KHACH_HANG_ORDER_FORM');
        } else {
            $this->_beforeStatus = OrderCustomer::find($parameters['id'])->status;
        }

        $entity->client_id = array_key_exists('client_id', $parameters) ? $parameters['client_id'] : null;
        $entity->status = 1; //Chờ chủ hàng xác nhận

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

        $customer = $this->_customerRepository->getItemById($entity->customer_id);
        if (!empty($entity->client_id)) {
            $customer = $this->_customerRepository->getItemById($entity->client_id);
        }
        if ($customer) {
            $entity->customer_name = $customer->delegate;
            $entity->customer_mobile_no = $customer->mobile_no;
        }

        $entity->ETA_date_desired = $entity->ETA_date;
        $entity->ETA_time_desired = $entity->ETA_time;

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
                    'weight' => $goods['weight'],
                    'volume' => $goods['volume'],
                    'total_weight' => $goods['total_weight'],
                    'total_volume' => $goods['total_volume'],
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
    }

    public function afterSaveSuccess($entity, $parameters)
    {
        $customer = $this->_customerRepository->getItemById($entity->customer_id);
        $userIds = $customer ? [$customer->user_id] : [];
        $this->_notificationService->notifyClientToCustomer(isset($parameters['id']) ? 2 : 1, $userIds, [
            'order_customer_id' => $entity->id, 'order_customer_no' => $entity->order_no
        ]);
    }

    public function getEventByCustomerID($start, $end, $customerID)
    {
        $query = DB::table('order_customer as o')
            ->where('o.client_id', '=', $customerID)
            ->where('o.del_flag', '=', '0')
            ->where(function ($q) use ($start, $end) {
                $q->where([
                    ['o.ETD_date', '>=', $start],
                    ['o.ETA_date', '<=', $end],
                ])
                    ->orWhere([
                        ['o.ETD_date', '<=', $start],
                        ['o.ETA_date', '>=', $end],
                    ])
                    ->orWhere([
                        ['o.ETD_date', '<=', $start],
                        ['o.ETA_date', '>', $start],
                        ['o.ETA_date', '<=', $end],
                    ])
                    ->orWhere([
                        ['o.ETD_date', '>=', $start],
                        ['o.ETD_date', '<', $end],
                        ['o.ETA_date', '>=', $end],
                    ]);
            });

        $events = $query->get([
            'o.id as id',
            'o.code as title',
            'o.status as status',
            DB::raw('(CASE 
    WHEN o.status = 1 THEN "#f8f9fa"
    WHEN o.status = 2 THEN "#6c757d"
    WHEN o.status = 3 THEN "#9d5508" 
    WHEN o.status = 4 THEN "rgb(103, 139, 251)" 
    WHEN o.status = 5 THEN "#28a745" 
    WHEN o.status = 6 THEN "#343a40"
    WHEN o.status = 7 THEN "#aa315b"
                        ELSE "#ffff" END) AS color'),
            DB::raw('concat(o.ETA_date,\' \',o.ETA_time) as end'),
            DB::raw('concat(o.ETD_date,\' \',o.ETD_time) as start'),
        ]);
        return $events;
    }
}
