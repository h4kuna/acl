<?php

namespace h4kuna\Acl\Security;

use Nette\Security\Identity;

class IdentityFactory
{

	/**
	 * @param mixed $id
	 * @param array|\ArrayAccess|null $data
	 * @return Identity
	 */
	public function create($id, $data = null)
	{
		return new Identity($id, null, $data);
	}

}
