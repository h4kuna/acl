<?php

namespace h4kuna\Acl\Core\Security;

abstract class AuthenticatorFacadeAbstract implements AuthenticatorFacadeInterface
{

	public function createAuthenticatorStructureById($userId)
	{
		$data = $this->fetchUserById($userId);
		return $this->createAuthenticatorStructure($data);
	}

}
