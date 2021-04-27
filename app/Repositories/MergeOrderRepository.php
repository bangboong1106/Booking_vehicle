<?php

namespace App\Repositories;

use App\Common\AppConstant;
use App\Model\Entities\Order;
use App\Repositories\Base\CustomRepository;
use App\Validators\OrderValidator;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use stdClass;

class MergeOrderRepository extends CustomRepository
{
    protected $_fieldsSearch = ['order_no', 'customer_name', 'customer_mobile_no', 'status', 'ETD_date', 'ETA_date', 'note'];

    function model()
    {
        return Order::class;
    }

    public function validator()
    {
        return OrderValidator::class;
    }

    /**
     * @param Builder $query
     * @return mixed
     */
    protected function _withRelations($query)
    {
        return $query->with(['listGoods']);
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 50));
        $columns = [
            '*',
            'ld.title as name_of_location_destination_id',
            'la.title as name_of_location_arrival_id',
            'c.full_name as name_of_customer_id',
            'ai.username as ai_username',
            'ai.full_name as ai_full_name',
            'au.username as au_username',
            'au.full_name as au_full_name',
            'v.reg_no as reg_no',
            'd.full_name as driver_name',
            'op.payment_type',
            'pu.username as pu_username',
            'pu.full_name as pu_full_name',
            'op.goods_amount',
            'op.vat',
            'op.anonymous_amount'

        ];
        $queryBuilder = $this->search($query, $columns, true)->with(['insUser', 'updUser']);
        return $queryBuilder->paginate($perPage);
    }


    // Lấy danh sách đơn hàng cho màn hình Bảng ghép chuyến
    // CreatedBy nlhoang 18/08/2020
    public function getListForRouteBoard($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 50));
        $queryBuilder = $this->search($query, [], true);
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
        });
        return $queryBuilder->paginate($perPage);
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

    // Hàm build câu lệnh merge chuyến
    // CreatedBy nlhoang 07/09/2020
    protected function getQueryBuilder($columns)
    {
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

        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('locations as ld', $this->getTableName() . '.location_destination_id', '=', 'ld.id')
            ->leftJoin('locations as la', $this->getTableName() . '.location_arrival_id', '=', 'la.id')
            ->leftJoin('drivers as d', $this->getTableName() . '.primary_driver_id', '=', 'd.id')
            ->leftJoin('vehicle as v', $this->getTableName() . '.vehicle_id', '=', 'v.id')
            ->leftJoin('customer as c', $this->getTableName() . '.customer_id', '=', 'c.id')
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id')
            ->leftJoin('order_payment as op', $this->getTableName() . '.id', '=', 'op.order_id')
            ->leftJoin('admin_users as pu', 'op.payment_user_id', '=', 'pu.id')
            ->whereIn($this->getTableName() . '.customer_id', $customerIDs)
            ->whereNotIn($this->getTableName() . '.status', [config('constant.KHOI_TAO'), config('constant.HUY')])
            ->where(function ($query) {
                $query->whereNull($this->getTableName() . '.route_id')
                    ->orWhere($this->getTableName() . '.route_id', '=', 0);
            })
            ->orderBy($this->getSortField(), $this->getSortType());
    }
}
