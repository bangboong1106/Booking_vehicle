<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Model\Concerns;


trait HasRole
{
	public function getRole() {
		return $this->getAttribute('role');
	}

	public function isSuperAdmin() {
		return $this->getAttribute('id') == getSystemConfig('default_auth_id');
	}

	public function getRoleAllowed($entity = '') {
		if ($this->isSuperAdmin()) {
			return true;
		}

		if (empty($entity)) {
			return false;
		}

		$currentRole = $this->getRole();
		$roles = config('role.' . $currentRole);

		if (in_array($currentRole, $roles)) {
			return true;
		}

		return false;
	}
}