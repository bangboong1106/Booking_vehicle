<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;
use App\Repositories\ContractTypeRepository;


class Contract extends ModelSoftDelete
{
    protected $table = "contracts";
    protected $_alias = 'contract';

    protected $fillable = ['customer_id', 'contract_no', 'file_id', 'issue_date', 'expired_date', 'type', 'status', 'note'];
    protected static $_destroyRelations = ['contractFile','contractType'];
    protected $_detailNameField = 'contract_no';

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function contractType()
    {
        return $this->hasOne(ContractType::class, 'id', 'type');
    }

    public function getStatus()
    {
        $statuses = config('system.contract_status');

        return array_key_exists($this->status, $statuses) ? $statuses[$this->status] : '';
    }

    public function contractFile()
    {
        return $this->hasOne(File::class, 'file_id', 'file_id');
    }
}