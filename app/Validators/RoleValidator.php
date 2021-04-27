<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class AdminUserInfo
 * @package App\Validator
 */
class RoleValidator extends BaseValidator
{
    protected function _getRulesDefault()
    {
        $rules = [
            'title' => 'required|max:255',
            'name' => 'required_without:id'. $this->_getUniqueInDbRule(false, ['name', 'id']),
        ];
        return $rules;
    }

    protected function _getMessagesDefault()
    {
        return [
            'name.required_without' => trans('validation.required'),
        ];
    }
}