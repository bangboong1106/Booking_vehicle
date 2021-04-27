<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:22
 */

namespace App\Repositories;


use App\Model\Entities\Ward;
use App\Repositories\Base\CustomRepository;
use App\Validators\WardValidator;
use DB;

class WardRepository extends CustomRepository
{
    function model()
    {
        return Ward::class;
    }

    public function validator()
    {
        return WardValidator::class;
    }

    protected function _withRelations($query)
    {
        return $query->with(['district' => function ($query) {
            $query->with('province');
        }]);
    }

    public function findFirstOrNew($data, $forUpdate = false)
    {
        $id = isset($data['id']) ? $data['id'] : 0;
        if (!$id) {
            $entity = clone $this->setRawAttributes($data);
            return $this->_prepareRelation($entity, $data, $forUpdate);
        }
        $entity = $this->findWithRelation($id);
        if (empty($entity)) {
            $entity = clone $this->setRawAttributes($data);
            return $this->_prepareRelation($entity, $data, $forUpdate);
        }
        return $this->_prepareRelation($entity->mergeAttributes($data), $data, $forUpdate);
    }

    public function findFirstOrNewByWardId($data)
    {
        $entity = $this->search([
            'ward_id_eq' => $data['ward_id']
        ])->first();
        if (empty($entity)) {
            $entity = clone $this->setRawAttributes($data);
            return $this->_prepareRelation($entity, $data);
        }
        return $this->_prepareRelation($entity->mergeAttributes($data), $data);
    }

    public function getWard($district_id, $wardTitle)
    {
        return Ward::query()->where('district_id', '=', $district_id)->whereRaw('LOWER(title) LIKE ? ', [trim(strtolower($wardTitle)) . '%'])->first();
    }
}