<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class ContractTypeValidator
 * @package App\Validator
 */
class ContractTypeValidator extends BaseValidator
{
    protected function _getRulesDefault()
    {
        $rules = [
            'name' => 'required'
        ];
        return $rules;
    }
}