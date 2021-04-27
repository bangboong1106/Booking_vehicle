<?php

namespace App\Validators;

use App\Validators\Base\BaseValidator;

/**
 * Class AdminUserInfo
 * @package App\Validator
 */
class UserValidator extends BaseValidator
{
	/**
	 * @param $data
	 * @return bool
	 */
	public function validateLogin($data)
	{
		$rules = array(
			'email' => 'required|email', // make sure the email is an actual email
			'password' => 'required' // password can only be alphanumeric and has to be greater than 3 characters
		);
		$messages = [
			'email.required' => trans('auth.id_required'),
			'email.email' => trans('auth.email_password_invalid'),
			'password.required' => trans('auth.password_required'),
		];
		return $this->_addRules($rules, $messages)->with($data)->passes();
	}
}