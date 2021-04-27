<?php

/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Entities\LocationGroup;
use App\Repositories\Base\CustomRepository;
use App\Validators\LocationGroupValidator;

class LocationGroupRepository extends CustomRepository
{
    function model()
    {
        return LocationGroup::class;
    }

    public function validator()
    {
        return LocationGroupValidator::class;
    }

    public function getListForSelect()
    {
        return $this->search()->get()->pluck('title', 'id');
    }

    /**
     * @param LocationGroup $entity
     * @param $data
     * @param bool $forUpdate
     * @return LocationGroup
     */
    protected function _prepareRelation($entity, $data, $forUpdate = false)
    {
        $entity = parent::_prepareRelation($entity, $data, $forUpdate);
        $entity->location_ids = isset($data['location_ids']) ? $data['location_ids'] : null;
        return $entity;
    }

    public function getItemsByUserID($all, $q, $customerId)
    {
        $query = LocationGroup::select("location_group.id", "location_group.title as title", "location_group.code as code", 'location_group.customer_id as customer_id')
                ->where(function ($query) use ($q) {
                    $query->where('location_group.code', 'LIKE', '%' . $q . '%')
                        ->orWhere('location_group.title', 'LIKE', '%' . $q . '%');
                });

        if ($customerId > 0) {
            $query = $query->where('customer_id', '=', $customerId);
        }

        $query = $query->orderBy('location_group.code', 'asc')
                ->paginate(10);
                                
        return $query;
    }

    protected function getKeyValue()
    {
        return [
            'name_of_customer' => [
                'filter_field' => 'c.full_name',
            ],
        ];
    }

    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('customer as c', 'c.id', '=', $this->getTableName() . '.customer_id')
            ->orderBy($this->getSortField(), $this->getSortType());
    }
}
