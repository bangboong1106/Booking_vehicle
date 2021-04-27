<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class VehicleConfigFileValidator
 * @package App\Validator
 */
class VehicleConfigFileValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'file_name' => 'required|max:256|unique:vehicle_config_file,file_name,' . $this->getData('id'),
        ];
        return $rules;
    }
}