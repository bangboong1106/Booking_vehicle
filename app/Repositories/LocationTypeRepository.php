<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Entities\LocationType;
use App\Repositories\Base\CustomRepository;
use App\Validators\LocationTypeValidator;

class LocationTypeRepository extends CustomRepository
{
    function model()
    {
        return LocationType::class;
    }

    public function validator()
    {
        return LocationTypeValidator::class;
    }

    public function getListForSelect()
    {
        return $this->search()->get()->pluck('title', 'id');
    }

    protected function getKeyValue()
    {
        return [
            'name_of_customer' => [
                'filter_field' => 'c.full_name',
            ],
        ];
    }

    public function getListForBackend($query)
    {
        $perPage = isset($query['per_page']) ? $query['per_page']
            : backendPaginate('per_page.' . $this->getModel()->getAlias(), backendPaginate('per_page.default', 20));
        $columns = [
            '*',
            'c.full_name as name_of_customer'
        ];
        $queryBuilder = $this->search($query, $columns);
        return $queryBuilder->paginate($perPage);
    }

    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('customer as c', function ($join) {
                $join->on('c.id', '=', $this->getTableName() . '.customer_id')
                    ->whereNull('c.parent_id');
            })
            ->orderBy($this->getSortField(), $this->getSortType());
    }
}