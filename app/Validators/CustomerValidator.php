<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;
use Illuminate\Validation\Rule;

/**
 * Class customerInfo
 * @package App\Validator
 */
class CustomerValidator extends BaseValidator
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
            'delegate' => 'required'
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
            'type' => 'required',
            'delegate' => 'required_if:type,1',
            'tax_code' => 'required_if:type,1',
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
            '*.delegate' => 'required',
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
            '*.delegate' => 'required',
        ];
        return [
            'rules' => $this->_buildRules($rules, false)
        ];
    }

    protected function _getMessagesDefault()
    {
        return [
            'delegate.required_if' => trans('validation.required', ['attribute' => trans('models.customer.attributes.delegate')]),

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

            '*.customer_code.distinct' => trans('validation.distinct', ['attribute' => trans('models.customer.attributes.customer_code')]),
            '*.customer_code.required' => trans('validation.required', ['attribute' => trans('models.customer.attributes.customer_code')]),
            '*.customer_code.max' => trans('validation.max.string', ['attribute' => trans('models.customer.attributes.customer_code')]),
            '*.customer_code.regex' => trans('validation.regex', ['attribute' => trans('models.customer.attributes.customer_code')]),
            '*.customer_code.unique' => trans('validation.unique', ['attribute' => trans('models.customer.attributes.customer_code')]),
            '*.customer_code.exists' => trans('validation.exists', ['attribute' => trans('models.customer.attributes.customer_code')]),

            '*.mobile_no.distinct' => trans('validation.distinct', ['attribute' => trans('models.customer.attributes.mobile_no')]),
            '*.mobile_no.required' => trans('validation.required', ['attribute' => trans('models.customer.attributes.mobile_no')]),
            '*.mobile_no.max' => trans('validation.max.string', ['attribute' => trans('models.customer.attributes.mobile_no')]),
            '*.mobile_no.unique' => trans('validation.unique', ['attribute' => trans('models.customer.attributes.mobile_no')]),

            '*.full_name.required' => trans('validation.required', ['attribute' => trans('models.customer.attributes.full_name')]),
            '*.delegate.required' => trans('validation.required', ['attribute' => trans('models.customer.attributes.delegate')]),
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
