<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class customerInfo
 * @package App\Validator
 */
class CurrencyValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'currency_code' => 'required|max:15|unique:currency,currency_code,' . $this->getData('id'),
            'currency_name' => 'required|max:256',
        ];
        return $rules;
    }

}