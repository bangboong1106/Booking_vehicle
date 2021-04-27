<?php

namespace App\Repositories;

use App\Model\Entities\Currency;
use App\Repositories\Base\CustomRepository;
use DB;

class CurrencyRepository extends CustomRepository
{
    protected $_fieldsSearch = ['currency_code', 'currency_name'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Currency::class;
    }

    public function validator()
    {
        return \App\Validators\CurrencyValidator::class;
    }

    public function getListForSelect()
    {
        return $this->search([
            'sort_type' => 'desc',
            'sort_field' => 'currency_code'
        ])->get()->pluck('currency_code', 'id');
    }
}