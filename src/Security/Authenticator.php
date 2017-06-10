<?php

namespace h4kuna\Acl\Security;

use h4kuna\Acl,
	Nette\Security\Passwords;

class Authenticator
{

	/** @var IdentityFactory */
	protected $identityFactory;

	/** @var AuthenticatorFacadeInterface */
	protected $authenticatorFacade;

	/** @var User */
	protected $user;

	public function __construct(IdentityFactory $identityFactory, AuthenticatorFacadeInterface $authenticatorFacade, User $user)
	{
		$this->identityFactory = $identityFactory;
		$this->authenticatorFacade = $authenticatorFacade;
		$this->user = $user;
	}

	/**
	 * @param mixed $id
	 * @return User
	 * @throws Acl\IdentityNotFoundException
	 * @throws Acl\IdentityIsBlockedException
	 */
	public function loginById($id)
	{
		return $this->login($this->authenticatorFacade->createAuthenticatorStructureById($id), 'id');
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
		$rawData = $this->authenticatorFacade->fetchUserByUsername($username);
		$data = $this->authenticatorFacade->createAuthenticatorStructure($rawData);
		if (!Passwords::verify($password, $data->getPassword())) {
			throw new Acl\InvalidPasswordException();
		}
		return $this->login($data, 'password');
	}

	protected function login(AuthenticatorStructure $data, $method)
	{
		$this->user->login($this->identityFactory->create($data));
		$this->authenticatorFacade->loginSuccess($this->user, $method);
		return $this->user;
	}

}
