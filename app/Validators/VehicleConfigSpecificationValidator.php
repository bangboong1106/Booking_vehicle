<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class VehicleConfigFileValidator
 * @package App\Validator
 */
class VehicleConfigSpecificationValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        $rules = [
            'name' => 'required|max:256|unique:vehicle_config_specification,name,' . $this->getData('id'),
        ];
        return $rules;
    }
}