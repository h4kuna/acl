<?php

namespace h4kuna\Acl\Security;

interface Permission
{

	/**
	 * @param User $user
	 * @return bool
	 */
	function isGod(User $user);
}
