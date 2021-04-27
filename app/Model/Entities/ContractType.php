<?php

namespace App\Model\Entities;

use App\Model\Base\ModelSoftDelete;


class ContractType extends ModelSoftDelete
{
    protected $table = "contract_type";
    protected $_alias = 'contract_type';

    protected $fillable = ['name', 'description'];
    protected $_detailNameField = 'name';

}