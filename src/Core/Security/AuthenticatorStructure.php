<?php

namespace h4kuna\Acl\Core\Security;

class AuthenticatorStructure implements \Serializable
{

	/** @var array */
	private $data = [];

	/** @var int|string */
	private $id;

	/** @var string */
	private $password;

	/** @var mixed */
	private $blocked = TRUE;

	public function __construct($id)
	{
		$this->id = $id;
	}

	/**
	 * @param array $data
	 * @return self
	 */
	public function setData(array $data)
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * @param bool $blocked
	 */
	public function setBlocked($blocked)
	{
		$this->blocked = (bool) $blocked;
	}

	/**
	 * Password hash.
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/** @return array */
	public function getData()
	{
		return $this->data;
	}

	public function getId()
	{
		return $this->id;
	}

	/** @return TRUE */
	public function isBlocked()
	{
		return $this->blocked;
	}

	/**
	 * Fill password for validation if user is logged in via password.
	 * @param string $password
	 * @return self
	 */
	public function setPassword($password)
	{
		$this->password = $password;
		return $this;
	}

	public function serialize()
	{
		return serialize($this->data);
	}

	public function unserialize($serialized)
	{
		$this->data = unserialize($serialized);
		$this->blocked = FALSE;
	}

}
