<?php

namespace h4kuna\Acl\Security;

interface PermissionInterface
{

	/**
	 * @param User $user
	 * @return bool
	 */
	function isGod(User $user);
}
