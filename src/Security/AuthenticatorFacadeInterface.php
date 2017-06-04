<?php

namespace h4kuna\Acl\Security;

/**
 * @param T mean your favorite structure
 */
interface AuthenticatorFacadeInterface
{

	/**
	 * @param string|int $userId
	 * @return T
	 */
	function fetchUserById($userId);

	/**
	 * @param string $username
	 * @return T
	 */
	function fetchUserByUsername($username);

	/**
	 * @param User $user
	 * @param string $method - is logged by [password, id]
	 */
	function loginSuccess(User $user, $method);

	/**
	 * @param T $data
	 * @return AuthenticatorStructure
	 */
	function createAuthenticatorStructure($data);

	/**
	 * @param string|int $userId
	 * @return AuthenticatorStructure
	 */
	function createAuthenticatorStructureById($userId);
}
