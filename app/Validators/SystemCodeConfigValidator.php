<?php

namespace App\Validators;

use App\Rules\CheckPhoneRule;
use App\Validators\Base\BaseValidator;

/**
 * Class SystemCodeConfigValidator
 * @package App\Validator
 */
class SystemCodeConfigValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'prefix' => 'required',
            'suffix_length' => 'required'
        ];
        return $rules;
    }

}