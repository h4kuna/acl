<?php

namespace h4kuna\Acl\Core\Security;

class IdentityFactory
{

	/**
	 * @param AuthenticatorStructure $data
	 * @return Identity
	 */
	public function create(AuthenticatorStructure $data)
	{
		return (new Identity($data->getId()))->setData($data);
	}

}
