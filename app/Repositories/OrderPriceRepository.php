<?php

namespace App\Repositories;

use App\Model\Entities\OrderPrice;
use App\Repositories\Base\CustomRepository;
use App\Validators\OrderPriceValidator;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderPriceRepository extends CustomRepository
{
    protected $_fieldsSearch = ['name', 'code'];

    function model()
    {
        return OrderPrice::class;
    }

    public function validator()
    {
        return OrderPriceValidator::class;
    }

    protected function _withRelations($query)
    {
        return $query->with(['order', 'priceQuote']);
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 50));
        $columns = [
            '*',
            'l1.title as destination_location_title',
            'l2.title as arrival_location_title',
        ];
        $queryBuilder = $this->search($query, $columns);
        return $queryBuilder->paginate($perPage);
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 07/09/2020
    protected function getKeyValue()
    {
        return [
            'name_of_price_quote_id' => [
                'filter_field' => 'pq.name',
            ],
            'name_of_location_destination_id' => [
                'filter_field' => 'l1.title',
            ],
            'name_of_location_arrival_id' => [
                'filter_field' => 'l2.title',
            ],
            'name_of_ins_id' => [
                'filter_field' => 'ai.username'
            ],
            'name_of_upd_id' => [
                'filter_field' => 'au.username',
            ]
        ];
    }

    // Hàm build câu lệnh đơn giá đơn hàng
    // CreatedBy nlhoang 07/09/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('orders as o', $this->getTableName() . '.order_id', '=', 'o.id')
            ->leftJoin('locations as l1', 'l1.id', '=', 'o.location_destination_id')
            ->leftJoin('locations as l2', 'l2.id', '=', 'o.location_arrival_id')
            ->leftJoin('price_quote as pq', $this->getTableName() . '.price_quote_id', '=', 'pq.id')
            ->leftJoin('admin_users as ai', $this->getTableName() . '.ins_id', '=', 'ai.id')
            ->leftJoin('admin_users as au', $this->getTableName() . '.upd_id', '=', 'au.id')
            ->where('is_approved', '=', 0)
            ->orderBy($this->getSortField(), $this->getSortType());
    }

    // Lấy danh sách đơn giá đơn hàng theo danh sách ID đơn hàng
    // CreatedBy nlhoang 04/08/2020
    public function getOrderPricesByIds($ids = [])
    {
        if (empty($ids)) {
            return [];
        }

        return $this->search([$this->getTableName() . '.id_in' => $ids], [])->get();
    }
}
