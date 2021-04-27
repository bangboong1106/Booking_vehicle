<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Entities\Province;
use App\Repositories\Base\CustomRepository;
use App\Validators\ProvinceValidator;
use DB;

class ProvinceRepository extends CustomRepository
{
    function model()
    {
        return Province::class;
    }

    public function validator()
    {
        return ProvinceValidator::class;
    }

    protected function _withRelations($query)
    {
        return $query->with('districts');
    }

    public function getListForSelect()
    {
        return $this->search([
            'sort_type' => 'asc',
            'sort_field' => 'title'
        ])->get()->pluck('title', 'province_id');
    }

    public function findFirstOrNewByProvinceId($data)
    {
        $entity = $this->search([
            'province_id_eq' => $data['province_id']
        ])->first();
        if (empty($entity)) {
            $entity = clone $this->setRawAttributes($data);
            return $this->_prepareRelation($entity, $data);
        }
        return $this->_prepareRelation($entity->mergeAttributes($data), $data);
    }

    public function getProvince($provinceTitle)
    {
        return Province::query()->whereRaw('LOWER(title) LIKE ? ', [trim(strtolower($provinceTitle)) . '%'])->first();

    }
}