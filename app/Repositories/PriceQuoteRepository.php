<?php

namespace App\Repositories;

use App\Model\Entities\Order;
use App\Model\Entities\OrderCustomer;
use App\Model\Entities\PriceQuote;
use App\Repositories\Base\CustomRepository;
use App\Validators\PriceQuoteValidator;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PriceQuoteRepository extends CustomRepository
{
    protected $_fieldsSearch = ['name', 'code'];

    function model()
    {
        return PriceQuote::class;
    }

    public function validator()
    {
        return PriceQuoteValidator::class;
    }

    protected function _withRelations($query)
    {
        return $query->with(['customerGroups', 'formulas', 'pointCharges']);
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
            DB::raw('group_concat(cg.name SEPARATOR \';\') as customer_groups'),
        ];
        $queryBuilder = $this->search($query, $columns);
        return $queryBuilder->paginate($perPage);
    }

    public function getListForExport($query)
    {
        $limit = isset($query['limit']) ? (int)$query['limit'] : backendPaginate('per_page.export_csv');
        $columns = ['*'];

        $queryBuilder = $this->search($query, $columns);
        return $this->_withRelations($queryBuilder)->paginate($limit, ['*'], 'page', 1);
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 07/09/2020
    protected function getKeyValue()
    {
        return [
            'customer_groups' => [
                'filter_field' => 'cg.name',
            ],
        ];
    }

    // Hàm build câu lệnh
    // CreatedBy nlhoang 07/09/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('price_quote_customer_group as pqcg', $this->getTableName() . '.id', '=', 'pqcg.price_quote_id')
            ->leftJoin('customer_group as cg', 'cg.id', '=', 'pqcg.customer_group_id')
            ->orderBy($this->getSortField(), $this->getSortType())
            ->groupBy($this->getTableName() . '.id');
    }

    public function getPriceQuotes($request)
    {
        $customerId = $request['customerId'];
        $customerGroupIds = DB::table('customer')
            ->join(
                'customer_group_customer',
                'customer_group_customer.customer_id',
                '=',
                'customer.id'
            )
            ->where([
                ['customer.id', $customerId],
                ['customer_group_customer.del_flag', 0]
            ])
            ->get(['customer_group_customer.customer_group_id'])
            ->pluck('customer_group_id')->toArray();
        $query = PriceQuote::leftJoin('price_quote_customer_group', 'price_quote_customer_group.price_quote_id', '=', 'price_quote.id')
            ->where(function ($query) use ($customerGroupIds) {
                $query->whereIn('price_quote_customer_group.customer_group_id', $customerGroupIds)
                    ->orWhere('price_quote.isApplyAll', 1);
            })
            ->where(function ($query) {
                $query->where('code', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('name', 'LIKE', '%' . request('q') . '%')
                    ->orWhere('description', 'LIKE', '%' . request('q') . '%');
            })
            ->where(function ($query) {
                $query->where(function ($sub_query) {
                    $sub_query->whereNull("price_quote.date_from")
                        ->whereNull("price_quote.date_to");
                })->orWhere(function ($sub_query) {
                    $sub_query->whereNull("price_quote.date_from")
                        ->whereDate("price_quote.date_to", '>=', DB::raw('now()'));
                })->orWhere(function ($sub_query) {
                    $sub_query->whereDate("price_quote.date_from", '<=', DB::raw('now()'))
                        ->whereNull("price_quote.date_to");
                })->orWhere(function ($sub_query) {
                    $sub_query->whereDate("price_quote.date_from", '<=', DB::raw('now()'))
                        ->whereDate("price_quote.date_to", '>=',  DB::raw('now()'));
                });
            })
            ->select(
                "price_quote.id",
                "price_quote.type",
                "price_quote.code as title",
                "price_quote.name",
                "price_quote.description"
            );
        $query = $query->where('price_quote.del_flag', '=', '0')->distinct()
            ->orderBy('price_quote.isApplyAll', 'asc')
            ->orderBy('price_quote.ins_date', 'desc')
            ->paginate(10);
        return $query;
    }

    public function getFormulas($priceQuoteId)
    {
        $query = DB::table('price_quote_formula as pq')
            ->leftJoin(
                'location_group as lg1',
                'lg1.id',
                '=',
                'pq.location_group_destination_id'
            )
            ->leftJoin(
                'location_group as lg2',
                'lg2.id',
                '=',
                'pq.location_group_arrival_id'
            )
            ->leftJoin(
                'm_vehicle_group as vg',
                'vg.id',
                '=',
                'pq.vehicle_group_id'
            )
            ->where([
                ['pq.price_quote_id', $priceQuoteId],
                ['pq.del_flag', 0]
            ])
            ->get([
                'pq.*',
                'lg1.title as name_of_location_group_destination_id',
                'lg2.title as name_of_location_group_arrival_id',
                'vg.name as name_of_vehicle_group_id'

            ]);
        return $query;
    }

    public function getPointCharges($priceQuoteId)
    {
        $query = DB::table('price_quote_point_charge as pq')
            ->leftJoin(
                'm_vehicle_group as vg',
                'vg.id',
                '=',
                'pq.vehicle_group_id'
            )
            ->where([
                ['pq.price_quote_id', $priceQuoteId],
                ['pq.del_flag', 0]
            ])
            ->get([
                'pq.*',
                'vg.name as name_of_vehicle_group_id'

            ]);
        return $query;
    }

    public function getCustomerGroups($priceQuoteId)
    {
        $query = DB::table('price_quote_customer_group as pq')
            ->leftJoin(
                'customer_group as vg',
                'vg.id',
                '=',
                'pq.customer_group_id'
            )
            ->where([
                ['pq.price_quote_id', $priceQuoteId],
                ['pq.del_flag', 0]
            ])
            ->get([
                'pq.*',
                'vg.name as name_of_customer_group_id'

            ]);
        return $query;
    }
}
