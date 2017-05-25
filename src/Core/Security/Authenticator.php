<?php

namespace h4kuna\Acl\Core\Security;

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
		$this->checkIdentity($data);
		if (!Passwords::verify($password, $data->getPassword())) {
			throw new Acl\InvalidPasswordException();
		}
		return $this->login($data, 'password');
	}

	protected function login(AuthenticatorStructure $data, $method)
	{
		$this->checkIdentity($data);
		if ($data->isBlocked()) {
			throw new Acl\IdentityIsBlockedException($data->getId());
		}
		$identity = $this->identityFactory->create($data);
		$this->user->login($identity);
		$this->authenticatorFacade->loginSuccess($this->user, $method);
		return $this->user;
	}

	/**
	 * @param AuthenticatorStructure $data
	 * @throws Acl\IdentityNotFoundException
	 */
	protected function checkIdentity(AuthenticatorStructure $data)
	{
		if (!$data->getId()) {
			throw new Acl\IdentityNotFoundException();
		}
	}

}
