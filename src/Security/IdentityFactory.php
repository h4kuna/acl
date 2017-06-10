<?php

namespace h4kuna\Acl\Security;

use h4kuna\Acl;

class IdentityFactory
{

	/**
	 * @param AuthenticatorStructure $data
	 * @return Identity
	 * @throws Acl\IdentityIsBlockedException
	 */
	public function create(AuthenticatorStructure $data)
	{
		if ($data->isBlocked()) {
			throw new Acl\IdentityIsBlockedException($data->getId());
		}
		return new Identity($data);
	}

}
