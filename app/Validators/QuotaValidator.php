<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:30
 */

namespace App\Validators;

use App\Validators\Base\BaseValidator;

class QuotaValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        return [
            'name' => 'required|unique:quota,name,' . $this->getData('id') . ',id,del_flag,0',
            'quota_code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:quota,quota_code,' . $this->getData('id'),
        ];
    }

    public function _buildImportRules($fromEditor = false)
    {
        return [
            'rules' => $this->_buildRules([
                '*.name' => 'required|unique:quota,name,NULL,id,del_flag,0',
                '*.quota_code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:quota,quota_code',
                '*.vehicle_group_id' => 'nullable|exists:m_vehicle_group,code',
                '*.location_destination_id' => 'nullable|exists:locations,code',
                '*.location_arrival_id' => 'nullable|exists:locations,code',
                '*.location_destination_group_id' => 'nullable|exists:location_group,code',
                '*.location_arrival_group_id' => 'nullable|exists:location_group,code',
            ])
        ];
    }

    public function _buildImportUpdateRules($fromEditor = false)
    {
        return [
            'rules' => $this->_buildRules([
                '*.name' => 'required|unique:quota,name,[*.id],id,del_flag,0',
                '*.quota_code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:quota,quota_code,[*.id],id,del_flag,0',
                '*.vehicle_group_id' => 'nullable|exists:m_vehicle_group,code',
                '*.location_destination_id' => 'nullable|exists:locations,code',
                '*.location_arrival_id' => 'nullable|exists:locations,code',
                '*.location_destination_group_id' => 'nullable|exists:location_group,code',
                '*.location_arrival_group_id' => 'nullable|exists:location_group,code',
            ])
        ];
    }

    protected function _getMessagesDefault()
    {
        return [
            '*.name.required' => trans('validation.required', ['attribute' => trans('models.quota.attributes.name')]),
            '*.quota_code.required' => trans('validation.required', ['attribute' => trans('models.quota.attributes.quota_code')]),

            '*.name.unique' => trans('validation.unique', ['attribute' => trans('models.quota.attributes.name')]),
            '*.quota_code.unique' => trans('validation.unique', ['attribute' => trans('models.quota.attributes.quota_code')]),
            '*.vehicle_group_id.exists' => trans('validation.exists', ['attribute' => trans('models.quota.attributes.vehicle_group_id')]),
            '*.location_destination_id.exists' => trans('validation.exists', ['attribute' => trans('models.quota.attributes.location_destination_id')]),
            '*.location_arrival_id.exists' => trans('validation.exists', ['attribute' => trans('models.quota.attributes.location_arrival_id')]),

            '*.quota_code.max' => trans('validation.max', ['attribute' => trans('models.quota.attributes.quota_code')]),
            '*.quota_code.regex' => trans('validation.regex', ['attribute' => trans('models.quota.attributes.quota_code')]),

            '*.distance.max' => trans('validation.max', ['attribute' => trans('models.quota.attributes.distance')]),

            '*.location_destination_group_id.exists' => trans('validation.exists', ['attribute' => trans('models.quota.attributes.location_destination_group_id')]),
            '*.location_arrival_group_id.exists' => trans('validation.exists', ['attribute' => trans('models.quota.attributes.location_group_arrival_id')]),

        ];
    }
}