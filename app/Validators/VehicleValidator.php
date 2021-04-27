<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;
use Illuminate\Validation\Rule;

/**
 * Class VehicleValidator
 * @package App\Validator
 */
class VehicleValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        return [
            'reg_no' => 'required|max:20|unique:vehicle,reg_no,' . $this->getData('id') . ',id,del_flag,0',
            'group_id' => 'required',
            'listDriver' => 'required',
            'partner_id' => 'required',
        ];
    }

    public function _buildImportRules($fromEditor = false)
    {
        $rules = [
            '*.reg_no' => 'required|max:20|distinct' . $this->_getUniqueInDbRule('vehicle', ['reg_no', 'id']),
            '*.group_code' => 'required|exists:m_vehicle_group,code',
            '*.driver_codes' => 'required|exists:drivers,code',
            '*.partner_id' => 'required',
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    public function _buildImportUpdateRules($fromEditor = false)
    {
        $rules = [
            '*.reg_no' => [
                Rule::exists('vehicle', 'reg_no')->where(function ($query) {
                    $query->where('del_flag', 0);
                })
            ],
            '*.group_code' => 'required|exists:m_vehicle_group,code',
            '*.driver_codes' => 'required|exists:drivers,code',
            '*.gps_company_id' => 'nullable|exists:gps_company,id',
            '*.partner_id' => 'required',
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    protected function _getMessagesDefault()
    {
        return [
            '*.reg_no.distinct' => trans('validation.distinct', ['attribute' => trans('models.vehicle.attributes.reg_no')]),
            '*.reg_no.unique' => trans('validation.unique', ['attribute' => trans('models.vehicle.attributes.reg_no')]),
            '*.reg_no.required' => trans('validation.required', ['attribute' => trans('models.vehicle.attributes.reg_no')]),
            '*.reg_no.exists' => trans('validation.exists', ['attribute' => trans('models.vehicle.attributes.reg_no')]),

            '*.group_code.required' => trans('validation.required', ['attribute' => trans('models.vehicle.attributes.group_id')]),
            '*.group_code.exists' => trans('validation.exists', ['attribute' => trans('models.vehicle.attributes.group_id')]),
            '*.driver_codes.required' => trans('validation.required', ['attribute' => trans('models.vehicle.attributes.driver_codes')]),
            '*.driver_codes.exists' => trans('validation.exists', ['attribute' => trans('models.vehicle.attributes.driver_codes')]),
            '*.gps_company_id.exists' => trans('validation.exists', ['attribute' => trans('models.vehicle.attributes.gps_company_id')]),
            '*.partner_id.required' => trans('validation.required', ['attribute' => trans('models.vehicle.attributes.partner_id')]),
        ];
    }
}