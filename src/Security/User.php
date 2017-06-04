<?php

namespace h4kuna\Acl\Security;

use Nette,
	Nette\Security\IAuthorizator;

class User extends Nette\Security\User
{

	public function isAllowed($resource = IAuthorizator::ALL, $privilege = IAuthorizator::ALL, ...$arguments)
	{
		return $this->getAuthorizator()->isAllowed($resource, $privilege, $this, $arguments);
	}

}
