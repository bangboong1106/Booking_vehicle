<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class AlertLogValidator
 * @package App\Validator
 */
class NotificationLogDriverValidator extends BaseValidator
{
    protected function _getRulesDefault()
    {
        $rules = [
            'title' => 'required',
            'message' => 'required'
        ];
        return $rules;
    }
}