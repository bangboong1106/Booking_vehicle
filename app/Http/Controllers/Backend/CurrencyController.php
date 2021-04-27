<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Base\BackendController;
use App\Repositories\CurrencyRepository;

/**
 * Class ContractController
 * @package App\Http\Controllers\Backend
 */
class CurrencyController extends BackendController
{
    public function __construct(CurrencyRepository $currencyRepository)
    {
        parent::__construct();
        $this->setRepository($currencyRepository);
        $this->setBackUrlDefault('currency.index');
        $this->setConfirmRoute('currency.confirm');
        $this->setMenu('category');
        $this->setTitle(trans('models.currency.name'));

    }
}
