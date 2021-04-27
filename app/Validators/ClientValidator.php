<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;
use Illuminate\Validation\Rule;

/**
 * Class ClientValidator
 * @package App\Validator
 */
class ClientValidator extends BaseValidator
{
    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        return [
            'adminUser.username' => 'required|max:256|unique:admin_users,username,' . $this->getData('adminUser.id'),
            'adminUser.email' => 'nullable|email|max:256|unique:admin_users,email,' . $this->getData('adminUser.id'),
            'customer_code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:customer,customer_code,' . $this->getData('id'),
            'mobile_no' => 'required|max:256|unique:customer,mobile_no,' . $this->getData('id') . ',id,del_flag,0',
            'full_name' => 'required',
            'delegate' => 'required_if:type,' . config("constant.CORPORATE_CUSTOMERS"),
            'birth_date' => 'date',
            'parent_id' => 'required',
        ];
    }

    protected function _buildClientApiRules()
    {
        $rules = [
            'username' => 'required|max:256|unique:admin_users,username,' . $this->getData('user_id'),
            'email' => 'nullable|email|max:256|unique:admin_users,email,' . $this->getData('user_id'),
            'customer_code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|unique:customer,customer_code,' . $this->getData('id'),
            'mobile_no' => 'required|max:256|unique:customer,mobile_no,' . $this->getData('id') . ',id,del_flag,0',
            'full_name' => 'required',
            'delegate' => 'required_if:type,' . config("constant.CORPORATE_CUSTOMERS"),
            'birth_date' => 'date',
            'parent_id' => 'required',
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    /**
     * @return array
     */
    protected function _buildCreateRules()
    {
        return [
            'rules' => $this->_buildRules([
                'adminUser.password' => 'required|min:6',
                'adminUser.password_confirmation' => 'required|same:adminUser.password|min:6',
            ]),
            'messages' => $this->_getMessagesDefault()
        ];
    }

    public function _buildImportRules($fromEditor = false)
    {
        $rules = [
            '*.adminUser.username' => 'required|max:256|distinct' . $this->_getUniqueInDbRule('admin_users', ['username', 'id']),
            '*.adminUser.email' => 'nullable|email|max:256|distinct' . $this->_getUniqueInDbRule('admin_users', ['email', 'id']),
            '*.adminUser.password' => 'required|min:6',
            '*.customer_code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|distinct' . $this->_getUniqueInDbRule(false, ['customer_code', 'id']),
            '*.mobile_no' => 'required|max:256|distinct' . $this->_getUniqueInDbRule(false, ['customer_code', 'id']),
            '*.full_name' => 'required',
            '*.delegate' => 'required_if:*.type,' . config("constant.CORPORATE_CUSTOMERS"),
            '*.birth_date' => 'nullable|date',
            '*.parent_code' => 'required|exists:customer,customer_code',
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    public function _buildImportUpdateRules($fromEditor = false)
    {
        $rules = [
            '*.adminUser.username' => 'required|max:256|distinct|unique:admin_users,username,[*.user_id],id,del_flag,0',
            '*.adminUser.email' => 'nullable|email|max:256|distinct|unique:admin_users,email,[*.user_id],id,del_flag,0',
            '*.adminUser.password' => 'nullable|min:6',
            '*.customer_code' => 'required|max:50|regex:/^[A-Za-z0-9\-! ,\'\"@\.:\(\)\\\$&#*?~\%\^\+_\/]+$/|distinct|exists:customer,customer_code',
            '*.mobile_no' => 'required|max:256|distinct' . $this->_getUniqueInDbRule(false, ['customer_code', 'id']),
            '*.full_name' => 'required',
            '*.delegate' => 'required_if:*.type,' . config("constant.CORPORATE_CUSTOMERS"),
            '*.birth_date' => 'nullable|date',
            '*.parent_code' => 'required|exists:customer,customer_code',
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    protected function _getMessagesDefault()
    {
        return [
            'delegate.required_if' => trans('validation.required', ['attribute' => trans('models.client.attributes.delegate')]),
            'parent_id.required' => trans('validation.required', ['attribute' => trans('models.client.attributes.parent_id')]),

            '*.adminUser.username.distinct' => trans('validation.distinct', ['attribute' => trans('models.admin.attributes.username')]),
            '*.adminUser.username.unique' => trans('validation.unique', ['attribute' => trans('models.admin.attributes.username')]),
            '*.adminUser.username.required' => trans('validation.required', ['attribute' => trans('models.admin.attributes.username')]),
            '*.adminUser.username.max' => trans('validation.max.string', ['attribute' => trans('models.admin.attributes.username')]),

            '*.adminUser.email.distinct' => trans('validation.distinct', ['attribute' => trans('models.admin.attributes.email')]),
            '*.adminUser.email.unique' => trans('validation.unique', ['attribute' => trans('models.admin.attributes.email')]),
            '*.adminUser.email.required' => trans('validation.required', ['attribute' => trans('models.admin.attributes.email')]),
            '*.adminUser.email.max' => trans('validation.max.string', ['attribute' => trans('models.admin.attributes.email')]),
            '*.adminUser.email.email' => trans('validation.email', ['attribute' => trans('models.admin.attributes.email')]),

            '*.adminUser.password.required' => trans('validation.required', ['attribute' => trans('models.admin.attributes.password')]),
            '*.adminUser.password.min' => trans('validation.min.string', ['attribute' => trans('models.admin.attributes.password')]),

            '*.customer_code.distinct' => trans('validation.distinct', ['attribute' => trans('models.client.attributes.customer_code')]),
            '*.customer_code.required' => trans('validation.required', ['attribute' => trans('models.client.attributes.customer_code')]),
            '*.customer_code.max' => trans('validation.max.string', ['attribute' => trans('models.client.attributes.customer_code')]),
            '*.customer_code.regex' => trans('validation.regex', ['attribute' => trans('models.client.attributes.customer_code')]),
            '*.customer_code.unique' => trans('validation.unique', ['attribute' => trans('models.client.attributes.customer_code')]),
            '*.customer_code.exists' => trans('validation.exists', ['attribute' => trans('models.client.attributes.customer_code')]),

            '*.mobile_no.distinct' => trans('validation.distinct', ['attribute' => trans('models.client.attributes.mobile_no')]),
            '*.mobile_no.required' => trans('validation.required', ['attribute' => trans('models.client.attributes.mobile_no')]),
            '*.mobile_no.max' => trans('validation.max.string', ['attribute' => trans('models.client.attributes.mobile_no')]),
            '*.mobile_no.unique' => trans('validation.unique', ['attribute' => trans('models.client.attributes.mobile_no')]),

            '*.full_name.required' => trans('validation.required', ['attribute' => trans('models.client.attributes.full_name')]),

            '*.delegate.required_if' => trans('validation.required_if', [
                'attribute' => trans('models.client.attributes.delegate'),
                'other' => trans('models.client.attributes.type'),
                'value' => config('system.customer_type.1'),
            ]),

            '*.birth_date.date' => trans('validation.date', ['attribute' => trans('models.client.attributes.birth_date')]),
            '*.parent_code.required' => trans('validation.required', ['attribute' => trans('models.client.attributes.parent_id')]),
            '*.parent_code.exists' => trans('validation.exists', ['attribute' => trans('models.client.attributes.parent_id')]),
        
            ];
    }

    /**
     * @param $data
     * @return bool
     */
    public function validateLogin($data)
    {
        $rules = array(
            'username' => 'required',
            'password' => 'required'
        );
        $messages = [
            'username.required' => trans('auth.id_required'),
            'password.required' => trans('auth.password_required'),
        ];
        return $this->_addRules($rules, $messages)->with($data)->passes();
    }
}
