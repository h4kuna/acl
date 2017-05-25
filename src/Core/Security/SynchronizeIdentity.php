<?php

namespace h4kuna\Acl\Core\Security;

use Nette\Caching,
	Nette\Utils;

class SynchronizeIdentity
{

	use \Nette\SmartObject;

	/** @var Caching\IStorage */
	private $storage;

	/** @var AuthenticatorFacadeInterface */
	private $authenticatorFacade;

	/** @var string */
	private $expiration = '+1 week';

	/** @var bool */
	private $storageHasUnlock;

	public function __construct(Caching\IStorage $storage, AuthenticatorFacadeInterface $authenticatorFacade)
	{
		$this->storage = $storage;
		$this->authenticatorFacade = $authenticatorFacade;
	}

	/**
	 * How long live in storage.
	 * @param string $expiration
	 */
	public function setExpiration($expiration)
	{
		$this->expiration = (string) $expiration;
	}

	/**
	 * Force refresh identity to another user.
	 * @param int $userId
	 */
	public function forceReloadUserIdentity($userId)
	{
		if (!$userId) {
			return;
		}
		$this->storage->remove(self::addNamespace($userId));
	}

	/**
	 * Load all users identities
	 */
	public function forceReloadUsersIdentity()
	{
		$this->storage->clean([Caching\Cache::ALL => TRUE]);
	}

	/**
	 * @param int|string $userId
	 * @return AuthenticatorStructure|NULL
	 */
	public function getIdentityData($userId)
	{
		if (!$userId) {
			return NULL;
		}

		$this->storage->lock($userId);
		$data = $this->storage->read(self::addNamespace($userId));
		if ($data) {
			$this->unlock($userId);
			return unserialize($data);
		}
		$structure = $this->authenticatorFacade->createAuthenticatorStructureById($userId);
		$this->saveIdentityData($userId, $structure);
		$this->unlock($userId);
		return $structure;
	}

	/**
	 * @param int|string $userId
	 * @param AuthenticatorStructure $structure
	 * @return void
	 */
	public function saveIdentityData($userId, AuthenticatorStructure $structure = NULL)
	{
		if ($structure === NULL) {
			$this->forceReloadUserIdentity($userId);
			return;
		}

		if ($structure->isBlocked() === FALSE) {
			$this->storage->write(self::addNamespace($userId), serialize($structure), [Caching\Cache::EXPIRATION => Utils\Datetime::from($this->expiration)->format('U')]);
		}
	}

	private function unlock($userId)
	{
		if ($this->storageHasUnlock === NULL) {
			$this->storageHasUnlock = method_exists($this->storage, 'unlock');
		}
		if ($this->storageHasUnlock) {
			// @todo maybe use interface?
			$this->storage->unlock($userId);
		}
	}

	private static function addNamespace($userId)
	{
		return 'synchronize.identity.' . $userId;
	}

}
