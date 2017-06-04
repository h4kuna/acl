<?php

namespace h4kuna\Acl\Test;

use h4kuna\Acl\Security;

class Permission implements \h4kuna\Acl\Security\PermissionInterface
{

	public function isGod(Security\User $user)
	{
		return (int) $user->getId() === 3;
	}

	public function resourceFiles(Security\User $user, $privilege)
	{
		if ($privilege === NULL) {
			return TRUE;
		} elseif (!$user->isLoggedIn()) {
			return FALSE;
		}

		if ($user->getId() === 1 && $privilege === 'checked') {
			return TRUE;
		}
		return FALSE;
	}

	public function resourceFile(Security\User $user, $privilege, $fileId)
	{
		if (!$user->isLoggedIn()) {
			return FALSE;
		}

		if ($privilege === 'read') {
			return $user->getId() === 1 && $fileId === 1;
		}
		return FALSE;
	}

}
