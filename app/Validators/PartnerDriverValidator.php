<?php

namespace App\Validators;

use App\Rules\CheckPhoneRule;
use App\Validators\Base\BaseValidator;
use Illuminate\Support\Facades\DB;

/**
 * Class PartnerDriverValidator
 * @package App\Validator
 */
class PartnerDriverValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        return [
            'adminUser.username' => 'required_if:create_account,1|max:256|unique:admin_users,username,[adminUser.id],id,del_flag,0',
            'adminUser.email' => 'nullable|max:256|unique:admin_users,email,' . $this->getData('adminUser.id'),
            'full_name' => 'required',
            'mobile_no' => 'required',
            'code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:drivers,code,' . $this->getData('id'),
        ];
    }

    /**
     * @return array
     */
    protected function _buildCreateRules()
    {
        return [
            'rules' => $this->_buildRules([
                'adminUser.password' => 'required_if:create_account,1|nullable|min:6',
                'adminUser.password_confirmation' => 'required_if:create_account,1|nullable|same:adminUser.password|min:6',
            ]),
            'messages' => $this->_getMessagesDefault()
        ];
    }

    protected function _getMessagesDefault()
    {
        return [
            'adminUser.username.required_if' => trans('validation.required'),
            'adminUser.password.required_if' => trans('validation.required'),
            'adminUser.password_confirmation.required_if' => trans('validation.required'),

            '*.adminUser.username.required' => trans('validation.required', ['attribute' => trans('models.admin.attributes.username')]),
            '*.adminUser.username.max' => trans('validation.max.string', ['attribute' => trans('models.admin.attributes.username')]),
            '*.adminUser.username.distinct' => trans('validation.distinct', ['attribute' => trans('models.admin.attributes.username')]),
            '*.adminUser.username.unique' => trans('validation.unique', ['attribute' => trans('models.admin.attributes.username')]),

            '*.adminUser.email.max' => trans('validation.max.string', ['attribute' => trans('models.admin.attributes.email')]),
            '*.adminUser.email.email' => trans('validation.email', ['attribute' => trans('models.admin.attributes.email')]),
            '*.adminUser.email.unique' => trans('validation.unique', ['attribute' => trans('models.admin.attributes.email')]),

            '*.adminUser.password.required' => trans('validation.required', ['attribute' => trans('models.admin.attributes.password')]),
            '*.adminUser.password.min' => trans('validation.min.string', ['attribute' => trans('models.admin.attributes.password')]),

            '*.full_name.required' => trans('validation.required', ['attribute' => trans('models.driver.attributes.full_name')]),
            '*.mobile_no.required' => trans('validation.required', ['attribute' => trans('models.driver.attributes.mobile_no')]),
            '*.mobile_no.regex' => trans('validation.regex', ['attribute' => trans('models.driver.attributes.mobile_no')]),

            '*.code.required' => trans('validation.required', ['attribute' => trans('models.driver.attributes.code')]),
            '*.code.max' => trans('validation.max', ['attribute' => trans('models.driver.attributes.code')]),
            '*.code.regex' => trans('validation.regex', ['attribute' => trans('models.driver.attributes.code')]),
            '*.code.unique' => trans('validation.unique', ['attribute' => trans('models.driver.attributes.code')]),
            '*.code.distinct' => trans('validation.distinct', ['attribute' => trans('models.driver.attributes.code')]),

            '*.vehicle_team_id.exists' => trans('validation.exists', ['attribute' => trans('models.driver.attributes.vehicle_team_id')]),
            '*.birth_date.date' => trans('validation.date', ['attribute' => trans('models.driver.attributes.birth_date')]),
            '*.work_date.date' => trans('validation.date', ['attribute' => trans('models.driver.attributes.work_date')]),
            ];
    }

    public function _buildImportRules($fromEditor = false)
    {
        return [
            'rules' => $this->_buildRules([
                '*.adminUser.username' => 'required|max:256|distinct' . $this->_getUniqueInDbRule('admin_users', ['username', 'id']),
                '*.adminUser.email' => 'nullable|max:256|email' . $this->_getUniqueInDbRule('admin_users', ['email', 'id']),
                '*.adminUser.password' => 'required|min:6',
                '*.full_name' => 'required',
                '*.mobile_no' => ['required', 'regex:/(0|\+84)\d{9,10}\b/'],
                '*.code' => 'required|distinct|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/' . $this->_getUniqueInDbRule(false, ['code', 'id']),
                '*.vehicle_team_id' => 'nullable|exists:vehicle_team,code',
                '*.birth_date' => 'nullable|date',
                '*.work_date' => 'nullable|date',
            ])
        ];
    }

    public function _buildImportUpdateRules($fromEditor = false)
    {
        $rules = [
            '*.adminUser.username' => 'nullable|max:256|distinct|unique:admin_users,username,[*.user_id],id,del_flag,0',
            '*.adminUser.email' => 'nullable|max:256|email|unique:admin_users,email,[*.user_id],id,del_flag,0',
            '*.adminUser.password' => 'nullable|min:6',
            '*.full_name' => 'required',
            '*.mobile_no' => ['required', 'regex:/(0|\+84)\d{9,10}\b/'],
            '*.code' => 'required|distinct|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:drivers,code,[*.id],id,del_flag,0',
            '*.vehicle_team_id' => 'nullable|exists:vehicle_team,code',
            '*.birth_date' => 'nullable|date',
            '*.work_date' => 'nullable|date',
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }
}