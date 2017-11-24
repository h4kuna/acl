<?php

namespace h4kuna\Acl\Security;

interface AuthenticatorFacade
{

	/**
	 * @param mixed $userId
	 * @return GlobalData|null
	 */
	function createGlobalDataById($userId);

	/**
	 * @param User $user
	 * @param string $method - is logged by [password, id]
	 */
	function loginSuccess(User $user, $method);

	/**
	 * @param string $username
	 * @return AuthenticatorStructure|null
	 */
	function createAuthenticatorStructureByUsername($username);

	/**
	 * @param string|int $userId
	 * @return AuthenticatorStructure|null
	 */
	function createAuthenticatorStructureById($userId);
}
