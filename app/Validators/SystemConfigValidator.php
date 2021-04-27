<?php

namespace App\Validators;

use App\Rules\CheckPhoneRule;
use App\Validators\Base\BaseValidator;

/**
 * Class SystemConfigValidator
 * @package App\Validator
 */
class SystemConfigValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'key' => 'required|unique:system_config,key,' . $this->getData('id'),
            'value' => 'required'
        ];
        return $rules;
    }

}