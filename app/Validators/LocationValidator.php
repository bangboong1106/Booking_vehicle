<?php
/**
 * Created by IntelliJ IDEA.
 * User: shine
 * Date: 10/6/18
 * Time: 12:30
 */

namespace App\Validators;

use App\Validators\Base\BaseValidator;

class LocationValidator extends BaseValidator
{
    /** Default create
     * @return array
     */
    protected function _getRulesDefault()
    {
        return [
            'title' => 'required|max:255',
            'code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:locations,code,' . $this->getData('id'),
            'province_id' => 'nullable|exists:m_province,province_id',
            'district_id' => 'nullable|exists:m_district,district_id',
            'ward_id' => 'nullable|exists:m_ward,ward_id',
            'limited_day' => 'nullable|regex:/[0-9]*[,]?[0-9]+/',
            'customer_id' => 'required'
        ];
    }

    public function _buildImportRules($fromEditor = false)
    {
        return [
            'rules' => [
                '*.title' => 'required|max:255',
                '*.code' => 'max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:locations,code',
                '*.province_id' => 'nullable|exists:m_province,province_id',
                '*.district_id' => 'nullable|exists:m_district,district_id,province_id,[*.province_id]',
                '*.ward_id' => 'nullable|exists:m_ward,ward_id,district_id,[*.district_id]',
            ]
        ];
    }

    public function _buildImportUpdateRules($fromEditor = false)
    {
        return [
            'rules' => [
                '*.title' => 'required|max:255',
                '*.code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:locations,code,[*.id],id,del_flag,0',
                '*.province_id' => 'nullable|exists:m_province,province_id',
                '*.district_id' => 'nullable|exists:m_district,district_id,province_id,[*.province_id]',
                '*.ward_id' => 'nullable|exists:m_ward,ward_id,district_id,[*.district_id]',
            ]
        ];
    }

    protected function _getMessagesDefault()
    {
        return [
            '*.title.required' => trans('validation.required', ['attribute' => trans('models.location.attributes.title')]),
            '*.title.max' => trans('validation.max.string', ['attribute' => trans('models.location.attributes.title')]),

            '*.code.required' => trans('validation.required', ['attribute' => trans('models.location.attributes.code')]),
            '*.code.max' => trans('validation.max.string', ['attribute' => trans('models.location.attributes.code')]),
            '*.code.regex' => trans('validation.regex', ['attribute' => trans('models.location.attributes.code')]),
            '*.code.unique' => trans('validation.unique', ['attribute' => trans('models.location.attributes.code')]),

            '*.province_id.required' => trans('validation.required', ['attribute' => trans('models.location.attributes.province_id')]),
            '*.province_id.exists' => trans('validation.date', ['attribute' => trans('models.location.attributes.province_id')]),
            '*.district_id.required' => trans('validation.required', ['attribute' => trans('models.location.attributes.district_id')]),
            '*.district_id.exists' => trans('validation.date', ['attribute' => trans('models.location.attributes.district_id')]),
            '*.ward_id.exists' => trans('validation.date', ['attribute' => trans('models.location.attributes.ward_id')]),
            'customer_id.required' => trans('validation.required', ['attribute' => trans('models.location.attributes.name_of_customer_id')]),
        ];
    }
}