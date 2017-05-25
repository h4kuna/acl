<?php

namespace h4kuna\Acl\Core\Security;

use Nette;

/**
 * @method void onChangeIdentity(Identity $identity) event method
 */
class Identity implements Nette\Security\IIdentity, \Serializable
{

	use Nette\SmartObject;

	/** @var callback[] */
	public $onChangeIdentity;

	/** @var bool */
	private $isChanged = FALSE;

	/** @var int */
	private $id;

	/** @var array|NULL */
	private $data;

	public function __construct($id)
	{
		$this->id = $id;
	}

	/**
	 * Returns the ID of user
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Returns a list of roles that the user is a member of
	 * NOT IMPLEMENTED!
	 * @return array
	 */
	public function getRoles()
	{
		return [];
	}

	/**
	 * Returns a user data
	 * @return array|NULL
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Sets user data value
	 * @param string $key property name
	 * @param mixed $value value
	 */
	public function __set($key, $value)
	{
		if (!$this->isExists($key) || $this->data[$key] !== $value) {
			$this->data[$key] = $value;
			$this->isChanged = TRUE;
		}
	}

	/**
	 * @internal Is only for UserStorage
	 * @param AuthenticatorStructure|NULL $data
	 * @return self
	 */
	public function setData(AuthenticatorStructure $data = NULL)
	{
		$this->data = $data ? $data->getData() : NULL;
		return $this;
	}

	/**
	 * Returns user data value
	 * @param string $key property name
	 * @return mixed
	 */
	public function &__get($key)
	{
		if ($this->isExists($key)) {
			return $this->data[$key];
		}
		$x = NULL;
		return $x;
	}

	/**
	 * Is property defined?
	 * @param string $key property name
	 * @return bool
	 */
	public function __isset($key)
	{
		return $this->isExists($key);
	}

	/**
	 * Removes property
	 * @param string $key property name
	 * @throws Nette\MemberAccessException
	 */
	public function __unset($key)
	{
		if ($this->isExists($key)) {
			unset($this->data[$key]);
			$this->isChanged = TRUE;
		}
	}

	public function serialize()
	{
		if ($this->isChanged) {
			$this->onChangeIdentity($this);
		}
		return serialize($this->id);
	}

	public function unserialize($serialized)
	{
		$this->id = unserialize($serialized);
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	private function isExists($key)
	{
		return isset($this->data[$key]);
	}

}
