<?php

namespace h4kuna\Acl\Security;

use h4kuna\Acl;

class Authorizator implements \Nette\Security\IAuthorizator
{

	/** @var PermissionInterface */
	private $permission;

	/** @var bool[] */
	private $methods = [];

	public function __construct(PermissionInterface $permission)
	{
		$this->permission = $permission;
	}

	/**
	 * @param string $resource
	 * @param string $privilege
	 * @param User $user
	 * @param array $arguments
	 * @return bool
	 */
	public function isAllowed($resource, $privilege, $user, array $arguments = []) // : bool
	{
		if (!isset($this->methods[$resource])) {
			$method = 'resource' . ucfirst($resource);
			if (!method_exists($this->permission, $method)) {
				throw new Acl\MethodIsNotImplementedException('Let\'s implement method ' . get_class($this->permission) . '::' . $method . '($user, $privilege, [$argument, $_]) return bool.');
			}
			$this->methods[$resource] = $method;
		}


		if ($this->permission->isGod($user)) {
			return TRUE;
		}

		return $this->permission->{$this->methods[$resource]}($user, $privilege, ...$arguments);
	}

}
