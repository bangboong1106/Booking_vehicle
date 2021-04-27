<?php

namespace App\Validators;

use App\Rules\CheckPhoneRule;
use App\Validators\Base\BaseValidator;

/**
 * Class customerInfo
 * @package App\Validator
 */
class ContactValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'contact_name' => 'required',
            'phone_number' => 'required|max:256',
            'email' => 'sometimes|nullable|email|max:256',
        ];
        return $rules;
    }

}