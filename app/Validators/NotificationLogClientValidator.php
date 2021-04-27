<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class NotificationLogClientValidator
 * @package App\Validator
 */
class NotificationLogClientValidator extends BaseValidator
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