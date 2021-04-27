<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class AdminUserInfo
 * @package App\Validator
 */
class AdminUserInfo extends BaseValidator
{
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
            'username.required' => trans('validation.required', ['attribute' => trans('models.admin.attributes.username')]),
            'password.required' => trans('validation.required', ['attribute' => trans('models.admin.attributes.password')]),
        ];
        return $this->_addRules($rules, $messages)->with($data)->passes();
    }

    /**
     * @return array
     */
    protected function _getRulesDefault()
    {
        return [
            'username' => 'required|max:255' . $this->_getUniqueInDbRule(false, ['username','id']),
            'email' => 'required|email|max:256' . $this->_getUniqueInDbRule(),
            'password' => 'nullable|same:password_confirmation|min:6',
            'partner_id' => 'required_if:role,partner'
        ];
    }

    /**
     * @return array
     */
    protected function _buildCreateRules()
    {
        return [
            'rules' => $this->_buildRules([
                'password' => 'required|min:6',
                'password_confirmation' => 'required|same:password|min:6',
            ])
        ];
    }

    /**
     * @return array
     */
    protected function _buildUpdateRules()
    {
        return parent::_buildUpdateRules(); // TODO: Change the autogenerated stub
    }

    /**
     * @return array
     */
    protected function _buildSearchRules()
    {
        return parent::_buildSearchRules(); // TODO: Change the autogenerated stub
    }

    /**
     * @return array
     */
    protected function _buildDestroyRules()
    {
        return parent::_buildDestroyRules(); // TODO: Change the autogenerated stub
    }

    protected function _getMessagesDefault()
    {
        return [
            'partner_id.required_if' => trans('validation.required',['attribute' => trans('models.admin.attributes.partner_id')]),
        ];
    }

}