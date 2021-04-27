<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class AlertLogValidator
 * @package App\Validator
 */
class AlertLogValidator extends BaseValidator
{
    protected function _getRulesDefault()
    {
        $rules = [
            'name' => 'required',
            'title' => 'required',
            'content' => 'required'
        ];
        return $rules;
    }
}