<?php

namespace App\Repositories;

use App\Model\Entities\UserInfo;
use App\Repositories\Base\CustomRepository;
use App\Validators\UserValidator;

class UserRepository extends CustomRepository
{
	/**
	 * Specify Model class name
	 *
	 * @return string
	 */
	function model()
	{
		return UserInfo::class;
	}

	public function validator()
	{
		return UserValidator::class;
	}
}