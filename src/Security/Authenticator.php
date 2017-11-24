<?php

namespace h4kuna\Acl\Security;

use h4kuna\Acl,
	Nette\Security\Passwords;

class Authenticator
{

	/** @var User */
	protected $user;

	/** @var AuthenticatorFacade */
	protected $authenticatorFacade;

	/** @var IdentityFactory */
	protected $identityFactory;

	public function __construct(User $user, AuthenticatorFacade $authenticatorFacade, IdentityFactory $identityFactory)
	{
		$this->user = $user;
		$this->authenticatorFacade = $authenticatorFacade;
		$this->identityFactory = $identityFactory;
	}

	/**
	 * @param mixed $id
	 * @return User
	 * @throws Acl\IdentityNotFoundException
	 * @throws Acl\IdentityIsBlockedException
	 */
	public function loginById($id)
	{
		$data = $this->authenticatorFacade->createAuthenticatorStructureById($id);
		$this->checkAuthenticatorStructure($data);
		return $this->login($data, 'id');
	}

	/**
	 * @param mixed $username
	 * @param string $password
	 * @return User
	 * @throws Acl\IdentityNotFoundException
	 * @throws Acl\InvalidPasswordException
	 * @throws Acl\IdentityIsBlockedException
	 */
	public function loginByPassword($username, $password)
	{
		$data = $this->authenticatorFacade->createAuthenticatorStructureByUsername($username);
		$this->checkAuthenticatorStructure($data);
		if (!$this->verifyPassword($password, $data->password)) {
			throw new Acl\InvalidPasswordException();
		}
		return $this->login($data, 'password');
	}

	protected function checkAuthenticatorStructure(AuthenticatorStructure $data = null)
	{
		if ($data === null || !$data->id) {
			throw new Acl\IdentityNotFoundException;
		} elseif ($data->blocked === true) {
			throw new Acl\IdentityIsBlockedException;
		}
	}

	protected function login(AuthenticatorStructure $data, $method)
	{
		$this->user->login($this->identityFactory->create($data->id, $data->data));
		$this->authenticatorFacade->loginSuccess($this->user, $method);
		return $this->user;
	}

	protected function verifyPassword($password, $storedPassword)
	{
		return Passwords::verify($password, $storedPassword);
	}
}
