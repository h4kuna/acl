<?php

namespace h4kuna\Acl\Security;

use Nette\Caching;
use    Nette\Utils;

class GlobalIdentity
{

	use \Nette\SmartObject;

	/** @var Caching\IStorage */
	private $storage;

	/** @var AuthenticatorFacade */
	private $authenticatorFacade;

	/** @var string */
	private $expiration = '+1 week';

	/** @var bool */
	private $storageHasUnlock;

	/** @var GlobalData[] */
	private $data;

	public function __construct(Caching\IStorage $storage, AuthenticatorFacade $authenticatorFacade)
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
	 * @param mixed $userId
	 */
	public function reloadIdentity($userId)
	{
		if (!$userId) {
			return;
		}
		unset($this->data[$userId]);
		$this->storage->remove(self::addNamespace($userId));
	}

	/**
	 * @param mixed $userId
	 * @return GlobalData|null
	 */
	public function getData($userId)
	{
		if (!$userId) {
			return null;
		} elseif (!isset($this->data[$userId])) {
			$this->data[$userId] = $this->getGlobalData($userId);
		}
		return $this->data[$userId];
	}

	/**
	 * Load all users identities
	 */
	public function reloadUsersIdentities()
	{
		$this->storage->clean([Caching\Cache::ALL => true]);
	}

	/**
	 * @param mixed $userId
	 * @return GlobalData|null
	 */
	private function getGlobalData($userId)
	{
		$key = self::addNamespace($userId);
		$data = $this->storage->read($key);
		if ($data) {
			return unserialize($data);
		}
		$this->storage->lock($userId);
		$data = $this->storage->read($key);
		if ($data) {
			$this->unlock($userId);
			return unserialize($data);
		}
		$structure = $this->authenticatorFacade->createGlobalDataById($userId);
		if ($structure === null) {
			$this->reloadIdentity($userId);
			$this->unlock($userId);
			return null;
		}
		$this->save($userId, $structure);
		$this->unlock($userId);
		return $structure;
	}

	/**
	 * @param mixed $userId
	 * @param GlobalData $structure
	 */
	private function save($userId, GlobalData $structure)
	{
		$this->storage->write(self::addNamespace($userId), serialize($structure), [
			Caching\Cache::EXPIRATION => Utils\Datetime::from($this->expiration)
				->format('U'),
		]);
	}

	private function unlock($userId)
	{
		if ($this->storageHasUnlock === null) {
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
