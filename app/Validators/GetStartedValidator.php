<?php


namespace App\Validators;

use App\Validators\Base\BaseValidator;


class GetStartedValidator extends BaseValidator
{
    protected function _getRulesDefault()
    {
        return [
            'email' => 'required|max:256|email|regex:/^[a-z0-9]+[._-]?[a-z0-9]+[^\D]*@[a-z0-9]+(\.[s]+)*(\.[a-z]{2,3}){1,2}$/i'
        ];
    }

    protected function _getMessagesDefault()
    {
        return [
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Sai định dạng email.',
            'email.regex' => 'Sai định dạng email.',
            'email.max' => 'Email không được lớn hơn 256 ký tự.',
        ];
    }
}