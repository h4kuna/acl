<?php

namespace h4kuna\Acl\Core\Http;

use h4kuna\Acl\Core\Security,
	Nette\Http AS NHttp;

class UserStorage extends \Nette\Http\UserStorage
{

	const
		BLOCKED = 0b1000;

	/** @var Security\SynchronizeIdentity */
	private $synchronizeIdentity;

	public function __construct(NHttp\Session $session, Security\SynchronizeIdentity $synchronizeIdentity)
	{
		parent::__construct($session);
		$this->synchronizeIdentity = $synchronizeIdentity;
	}

	/**
	 * Returns current user identity, if any.
	 * @return Security\Identity|NULL
	 */
	public function getIdentity()
	{
		$identity = parent::getIdentity();
		if ($identity === NULL) {
			return NULL;
		}
		$identity->getData() === NULL && $this->fillDataToIdentity($identity);
		return $identity;
	}

	/**
	 * @param Security\Identity $identity type is right
	 */
	private function fillDataToIdentity(Security\Identity $identity)
	{
		$structure = $this->synchronizeIdentity->getIdentityData($identity->getId());
		$identity->setData($structure);


		if ($structure->isBlocked()) {
			$this->setAuthenticated(FALSE);
			$section = $this->getSessionSection(TRUE);
			$section->reason = self::BLOCKED;
			return;
		}

		$identity->onChangeIdentity[] = function($identity) {
			$this->synchronizeIdentity->forceReloadUserIdentity($identity->getId());
		};
	}

}
