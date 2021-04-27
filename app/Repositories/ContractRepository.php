<?php

namespace App\Repositories;

use App\Model\Entities\Contract;
use App\Repositories\Base\CustomRepository;
use Carbon\Carbon;
use DB;

class ContractRepository extends CustomRepository
{
    protected $_fieldsSearch = ['email', 'mobile_no', 'full_name', 'contract_code'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return contract::class;
    }

    public function validator()
    {
        return \App\Validators\ContractValidator::class;
    }

    protected function _withRelations($query)
    {
        return $query->with('customer', 'contractFile', 'contractType');
    }

    public function findFirstOrNew($data, $forUpdate = false)
    {
        $id = isset($data['id']) ? $data['id'] : 0;
        if (!$id) {
            $entity = clone $this->setRawAttributes($data);
            return $this->_prepareRelation($entity, $data, $forUpdate);
        }
        $entity = $this->find($id);

        empty($entity->issue_date) ? null : $entity->issue_date = Carbon::parse($entity->issue_date)->format('d-m-Y');
        empty($entity->expired_date) ? null : $entity->expired_date = Carbon::parse($entity->expired_date)->format('d-m-Y');

        if (empty($entity)) {
            $entity = clone $this->setRawAttributes($data);
            return $this->_prepareRelation($entity, $data, $forUpdate);
        }
        return $this->_prepareRelation($entity->mergeAttributes($data), $data, $forUpdate);
    }

    public function getContractWithID($id)
    {
        if ($id)
            return $this->search([
                'id_eq' => $id
            ])->first();
        return null;
    }

    // Hàm lấy các trường liên kết
    // CreatedBy nlhoang 03/09/2020
    protected function getKeyValue()
    {
        return [
            'full_name' => [
                'filter_field' => 'c.full_name',
            ],
            'type' => [
                'filter_field' => 'ct.name',
            ]
        ];
    }

    // Hàm build câu lệnh hợp đồng
    // CreatedBy nlhoang 03/09/2020
    protected function getQueryBuilder($columns)
    {
        return $this->getBuilder()->select($this->_buildColumn($columns))
            ->leftJoin('customer as c', 'contracts.customer_id', '=', 'c.id')
            ->leftJoin('contract_type as ct', 'contracts.type', '=', 'ct.id')
            ->orderBy($this->getSortField(), $this->getSortType());
    }
}
