<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class AlertLogValidator
 * @package App\Validator
 */
class NotificationLogValidator extends BaseValidator
{
    protected function _getRulesDefault()
    {
        $rules = [
            'title' => 'required',
            'content' => 'required'
        ];
        return $rules;
    }
}