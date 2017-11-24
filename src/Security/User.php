<?php

namespace h4kuna\Acl\Security;

use Nette;
use Nette\Security\IAuthorizator;
use Nette\Security\IUserStorage;

class User extends Nette\Security\User
{

	/** @var GlobalIdentity */
	private $globalIdentity;

	public function __construct(IUserStorage $storage, GlobalIdentity $globalIdentity = null, IAuthorizator $authorizator = null)
	{
		parent::__construct($storage, null, $authorizator);
		$this->globalIdentity = $globalIdentity;
		$this->onLoggedOut[] = function (self $user) {
			$this->globalIdentity->reloadIdentity($user->getId());
		};
	}

	public function reloadIdentity()
	{
		$this->globalIdentity->reloadIdentity($this->getId());
	}

	/**
	 * @return GlobalData
	 */
	public function getGlobalData()
	{
		return $this->globalIdentity->getData($this->getId());
	}

	/**
	 * @param null $resource
	 * @param null $privilege
	 * @param array ...$arguments
	 * @return bool
	 */
	public function isAllowed($resource = IAuthorizator::ALL, $privilege = IAuthorizator::ALL, ...$arguments)
	{
		return $this->getAuthorizator()->isAllowed($resource, $privilege, $this, $arguments);
	}

}
