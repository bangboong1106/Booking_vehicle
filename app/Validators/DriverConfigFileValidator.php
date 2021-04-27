<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class DriverConfigFileValidator
 * @package App\Validator
 */
class DriverConfigFileValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'file_name' => 'required|max:256|unique:driver_config_file,file_name,' . $this->getData('id'),
        ];
        return $rules;
    }
}