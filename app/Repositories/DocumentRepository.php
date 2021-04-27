<?php

namespace App\Repositories;

use App\Model\Entities\Order;
use App\Repositories\Base\CustomRepository;
use DB;
use Illuminate\Support\Str;
use stdClass;
use Illuminate\Support\Facades\Auth;

class DocumentRepository extends CustomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Order::class;
    }

    public function validator()
    {
        return \App\Validators\DocumentValidator::class;
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
            'v.reg_no as reg_no',
            'd.full_name as driver_name',
            'd.id as driver_id',
            'c.full_name as name_of_customer_id',
            'ld.title as name_of_location_destination_id',
            'la.title as name_of_location_arrival_id'
        ];
        $queryBuilder = $this->search($query, $columns)->with(['insUser', 'updUser']);
        return $queryBuilder->paginate($perPage);
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        $columns = [
            '*',
            'v.reg_no as reg_no',
            'd.full_name as driver_name',
            'd.id as driver_id',
            'c.full_name as customer_full_name',
        ];
        $queryBuilder = $this->search($query, $columns);
        return $this->_withRelations($queryBuilder)->paginate($limit, ['*'], 'page', 1);
    }

    protected function getKeyValue()
    {
        return [
            'vehicle' => [
                'filter_field' => 'v.reg_no',
            ],
            'reg_no' => [
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
            'name_of_customer_id' => [
                'filter_field' => 'c.full_name',
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
        ];
    }

    // Hàm build câu lệnh đơn hàng khách hàng
    // CreatedBy nlhoang 31/08/2020
    protected function getQueryBuilder($columns)
    {
        $partnerId = Auth::user()->partner_id;
        $customerIDs = DB::table('customer AS t1')
            ->leftJoin('customer_group_customer AS t2', 't2.customer_id', '=', 't1.id')
            ->leftJoin('admin_users_customer_group AS t3', 't3.customer_group_id', '=', 't2.customer_group_id')
            ->where('t1.del_flag', '=', 0)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('t3.admin_user_id', '=', Auth::User()->id)
                        ->orWhere('t3.del_flag', '=', 0);
                })
                    ->orWhereNull('t2.customer_id');
            })
            ->groupBy('t1.id')->pluck('t1.id as customer_id')->toArray();

        $query = $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('locations as ld', $this->getTableName() . '.location_destination_id', '=', 'ld.id')
            ->leftJoin('locations as la', $this->getTableName() . '.location_arrival_id', '=', 'la.id')
            ->leftJoin('drivers as d', $this->getTableName() . '.primary_driver_id', '=', 'd.id')
            ->leftJoin('vehicle as v', $this->getTableName() . '.vehicle_id', '=', 'v.id')
            ->leftJoin('customer as c', $this->getTableName() . '.customer_id', '=', 'c.id')
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id')
            ->whereIn($this->getTableName() . '.customer_id', $customerIDs);
        if ($partnerId) {
            $query->where($this->getTableName() . '.partner_id', '=', $partnerId);
        }
        $query->orderBy($this->getSortField(), $this->getSortType());
        return $query;
    }

    public function calcStatusDocumentsDaily()
    {
        $sql = 'call proc_calc_status_documents_daily ()';
        DB::select($sql);
    }

    // Lấy dữ liệu Xe để xuất theo mẫu
    // CreatedBy nlhoang 10/04/2020
    public function getExportByIDs($ids)
    {
        $ordersQuery = DB::table('orders')
            ->leftJoin('drivers', 'drivers.id', '=', 'orders.primary_driver_id')
            ->leftJoin('vehicle', 'orders.vehicle_id', '=', 'vehicle.id')
            ->leftJoin('customer', 'orders.customer_id', '=', 'customer.id')
            ->leftJoin(
                DB::raw('(SELECT locations.id, locations.full_address, locations.latitude, locations.longitude FROM locations) as location_destination'),
                'location_destination.id',
                '=',
                'orders.location_destination_id'
            )
            ->leftJoin(
                DB::raw('(SELECT locations.id, locations.full_address, locations.latitude, locations.longitude FROM locations) as location_arrival'),
                'location_arrival.id',
                '=',
                'orders.location_arrival_id'
            )
            ->where([
                ['orders.del_flag', '=', '0'],
            ])
            ->whereIn('orders.id', $ids);
        $status = convertCaseWhenQuery(config('system.order_status'), 'orders.status', 'status');
        $precedence = convertCaseWhenQuery(config('system.order_precedences'), 'orders.precedence', 'precedence');
        $status_collected_documents = convertCaseWhenQuery(config('system.collected_documents_combo'), 'orders.status_collected_documents', 'status_collected_documents');
        $is_collected_documents = convertCaseWhenQuery(config('system.option'), 'orders.is_collected_documents', 'is_collected_documents');


        $orders = $ordersQuery->get([
            'orders.id as order_id',
            'order_code',
            'orders.order_no',
            'orders.bill_no',
            DB::raw($status),
            DB::raw($precedence),
            DB::raw($status_collected_documents),
            DB::raw($is_collected_documents),
            'order_date', 'customer.full_name as customer_full_name', 'customer_name', 'customer_mobile_no',
            'orders.ETD_date', 'orders.ETD_time', 'orders.ETD_date_reality', 'orders.ETD_time_reality', 'contact_name_destination', 'contact_mobile_no_destination', 'location_destination_id', 'location_destination.full_address as location_destination', 'contact_email_destination',
            'location_destination.latitude as location_des_lat', 'location_destination.longitude as location_des_long', 'location_arrival.latitude as location_arr_lat', 'location_arrival.longitude as location_arr_long',
            'orders.ETA_date', 'orders.ETA_time', 'orders.ETA_date_reality', 'orders.ETA_time_reality', 'contact_name_arrival', 'contact_mobile_no_arrival', 'location_arrival_id', 'location_arrival.full_address as location_arrival', 'contact_email_arrival',
            'orders.good_details',
            'orders.amount', 'orders.quantity', 'orders.volume', 'orders.weight',
            'loading_arrival_fee', 'loading_destination_fee', 'description',
            'drivers.full_name',
            'vehicle.reg_no',
            'orders.note',
            'orders.date_collected_documents',
            'orders.time_collected_documents',
            'orders.commission_amount',
            'orders.date_collected_documents_reality', 'orders.time_collected_documents_reality', 'orders.num_of_document_page',
            'orders.document_type',
            'orders.document_note',
            DB::raw('CASE WHEN orders.commission_type = 1 THEN orders.commission_value ELSE 0 END AS commission_value'),
            DB::raw(' orders.amount - coalesce(orders.commission_amount, 0 ) as final_amount'),
            DB::raw('CASE WHEN orders.is_insured_goods = 1 THEN "Có" ELSE "Không" END as is_insured_goods'),
        ]);

        $locationQuery = DB::table('order_locations')
            ->join('locations', 'order_locations.location_id', '=', 'locations.id')
            ->whereIn('order_locations.order_id', $ids)
            ->where('order_locations.type', '=', 1);
        $listDestinationLocations = $locationQuery->get(['order_locations.order_id', 'order_locations.location_id', 'order_locations.date', 'order_locations.date_reality', 'order_locations.time', 'order_locations.time_reality', 'order_locations.type', 'locations.title', 'locations.full_address']);

        $locationQuery = DB::table('order_locations')
            ->join('locations', 'order_locations.location_id', '=', 'locations.id')
            ->whereIn('order_locations.order_id', $ids)
            ->where('order_locations.type', '=', 2);
        $listArrivalLocations = $locationQuery->get(['order_locations.order_id', 'order_locations.location_id', 'order_locations.date', 'order_locations.date_reality', 'order_locations.time', 'order_locations.time_reality', 'order_locations.type', 'locations.title', 'locations.full_address']);

        $data = [];
        foreach ($orders as $order) {
            $array_filter = [];
            foreach ($listDestinationLocations as $key => $var) {
                if (($var->{'order_id'} == $order->{'order_id'})) {
                    $array_filter[] = $var;
                }
            }
            $order->listDestinationLocations = $array_filter;

            $array_filter1 = [];
            foreach ($listArrivalLocations as $key => $var) {
                if (($var->{'order_id'} == $order->{'order_id'})) {
                    $array_filter1[] = $var;
                }
            }
            $order->listArrivalLocations = $array_filter1;
            $data[] = $order;
        }
        return $data;
    }

    public function getDocumentNoticeForDriver()
    {
        $documentNotices = DB::table('orders as o')
            ->where([
                ["o.del_flag", "=", 0],
            ])
            ->whereNotNull("o.primary_driver_id")
            ->groupBy("o.primary_driver_id")
            ->get([
                "o.primary_driver_id as driver_id",
                DB::raw("SUM(CASE WHEN o.status_collected_documents = 3 THEN 1 ELSE 0 END) total_late"),
                DB::raw("SUM(CASE WHEN o.status_collected_documents IN (4,5) THEN 1 ELSE 0 END) total_pending")
            ]);

        return $documentNotices;
    }
}
