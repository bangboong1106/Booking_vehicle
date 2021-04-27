<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class customerInfo
 * @package App\Validator
 */
class AccessoryValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'name' => 'required|max:255|unique:accessory,name,' . $this->getData('id'),
            'description' => 'max:255',
        ];
        return $rules;
    }

}