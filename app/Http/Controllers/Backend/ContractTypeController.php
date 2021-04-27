<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\ContractTypeRepository;

/**
 * Class ContractTypeController
 * @package App\Http\Controllers\Backend
 */
class ContractTypeController extends BackendController
{
    public function __construct(ContractTypeRepository $contractTypeRepository)
    {
        parent::__construct();
        $this->setRepository($contractTypeRepository);
        $this->setBackUrlDefault('contract-type.index');
        $this->setConfirmRoute('contract-type.confirm');
        $this->setMenu('category');
        $this->setTitle(trans('models.contract_type.name'));
    }

}
