<?php

namespace App\Model\Entities;

use App\Model\Base\Auth\User;
use Illuminate\Notifications\Notifiable;

class UserInfo extends User
{
	protected $table = "users";
	use Notifiable;
	protected $_alias = 'user';
	protected $fillable = ['name', 'email', 'password', 'phone_number', 'dob', 'mob', 'yob', 'fbId'];

	protected $hidden = ['password'];

	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = genPassword($value);
	}

	public function getAuthPassword()
	{
		return $this->password;
	}
}