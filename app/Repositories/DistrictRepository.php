<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Entities\District;
use App\Repositories\Base\CustomRepository;
use App\Validators\DistrictValidator;
use DB;

class DistrictRepository extends CustomRepository
{
    function model()
    {
        return District::class;
    }

    public function validator()
    {
        return DistrictValidator::class;
    }

    protected function _withRelations($query)
    {
        return $query->with('province');
    }

    public function findFirstOrNewByDistrictId($data)
    {
        $entity = $this->search([
            'district_id_eq' => $data['district_id']
        ])->first();
        if (empty($entity)) {
            $entity = clone $this->setRawAttributes($data);
            return $this->_prepareRelation($entity, $data);
        }
        return $this->_prepareRelation($entity->mergeAttributes($data), $data);
    }

    public function getDistrict($province_id, $districtTitle)
    {
        return District::query()->where('province_id', '=', $province_id)->whereRaw('LOWER(title) LIKE ? ', [trim(strtolower($districtTitle)) . '%'])->first();
    }
}