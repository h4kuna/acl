<?php

namespace h4kuna\Acl\Core\Security;

interface PermissionInterface
{

	/**
	 * @param User $user
	 * @return bool
	 */
	function isGod(User $user);
}
