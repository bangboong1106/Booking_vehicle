<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;
use Illuminate\Validation\Rule;

/**
 * Class customerInfo
 * @package App\Validator
 */
class CustomerDefaultDataValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        return [
            'code' => 'required',
            'customer_id' => 'required',
            'client_id' => 'required',
            'location_destination_id' => 'required',
            'location_arrival_id' => 'required',
        ];
    }

    protected function _buildClientApiRules()
    {
        $rules = [
            'client_id' => 'required',
            'code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:customer_default_data,code,' . $this->getData('code'),
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }
}
