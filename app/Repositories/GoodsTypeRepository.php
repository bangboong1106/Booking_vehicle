<?php

namespace App\Repositories;

use App\Model\Entities\GoodsType;
use App\Repositories\Base\CustomRepository;
use App\Validators\GoodsTypeValidator;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GoodsTypeRepository extends CustomRepository
{
    protected $_fieldsSearch = ['title', 'note'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return GoodsType::class;
    }

    public function validator()
    {
        return GoodsTypeValidator::class;
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
            'gg.name as name_of_goods_group_id',
            'c.full_name as name_of_customer_id'
        ];
        $queryBuilder = $this->search($query, $columns);
        return $queryBuilder->paginate($perPage);
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 07/10/2020
    protected function getKeyValue()
    {
        return [
            'name_of_goods_group_id' => [
                'filter_field' => 'gg.name',
            ],
            'name_of_customer_id' => [
                'filter_field' => 'c.full_name',
            ],
        ];
    }

    // Hàm build câu lệnhp
    // CreatedBy nlhoang 07/10/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('goods_group as gg', $this->getTableName() . '.goods_group_id', '=', 'gg.id')
            ->leftJoin('customer as c', function ($join) {
                $join->on('c.id', '=', $this->getTableName() . '.customer_id')
                    ->whereNull('c.parent_id');
            })
            ->orderBy($this->getSortField(), $this->getSortType());
    }

    public function getListForSelect()
    {
        return GoodsType::select(DB::raw('CONCAT(code, "|", title) AS title, id'))
            ->where('del_flag', '=', 0)
            ->pluck('title', 'id');
    }

    public function getTitleByIds($ids = [])
    {
        $result = "";
        if (empty($ids)) {
            return $result;
        }
        $result = $this->search(['id_in' => $ids], ['title'])->implode(',', 'title');
        return $result;
    }

    public function getGoodsTypeWithVolumeAndWeight()
    {
        $goodTypes = $this->search([])->get();
        $results = [];
        foreach ($goodTypes as $item) {
            $results[$item->code] = [
                'volume' => $item->volume,
                'weight' => $item->weight
            ];
        }
        return $results;
    }

    public function getGoodsTypeWithCustomerId($customerId)
    {
        $goodsTypeList = [];
        if (empty($customerId))
            return $goodsTypeList;

        $goodsTypes = $this->search([
            'customer_id_eq' => $customerId
        ])->get();

        if ($goodsTypes)
            foreach ($goodsTypes as $goods) {
                $goodsTypeList[$goods->id] = $goods;
            }
        return $goodsTypeList;
    }
}
