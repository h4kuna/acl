<?php

namespace h4kuna\Acl\Security;

use h4kuna\Acl\Test,
	Nette\Security,
	Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

class AuthenticatorTest extends \Tester\TestCase
{

	/** @var Authenticator */
	private $authenticator;

	/** @var Test\UserModel */
	private $userModel;

	/** @var Security\User */
	private $user;

	public function __construct(Authenticator $authenticator, Test\UserModel $userModel, Security\User $user)
	{
		$this->authenticator = $authenticator;
		$this->userModel = $userModel;
		$this->user = $user;
	}

	protected function tearDown()
	{
		$this->user->logout(TRUE);
	}

	public function testLoginById()
	{
		$user = $this->authenticator->loginById(1);
		Assert::false(isset($user->getIdentity()->password));
		Assert::false(isset($user->getIdentity()->block));
		Assert::false(isset($user->getIdentity()->id));
		Assert::same(1, $user->getId());
		Assert::true($user->isLoggedIn());
		Assert::same(2, $this->userModel->fetchUserById($user->getId())['login_count']);
	}

	public function testLoginByPassword()
	{
		$user = $this->authenticator->loginByPassword('Joe', '123456');
		Assert::same(1, $user->getId());
		Assert::true($user->isLoggedIn());
	}

	/**
	 * @throws \h4kuna\Acl\InvalidPasswordException
	 */
	public function testLoginByPasswordFailPassword()
	{
		$this->authenticator->loginByPassword('Joe', 'heslo');
	}

	/**
	 * @throws \h4kuna\Acl\IdentityNotFoundException
	 */
	public function testLoginByPasswordFailIdentity()
	{
		$this->authenticator->loginByPassword('doe.joe@gmail.com', 'password');
	}

	/**
	 * @throws \h4kuna\Acl\IdentityIsBlockedException
	 */
	public function testLoginByPasswordFaildBlocked()
	{
		$this->authenticator->loginByPassword('alfred', 'password');
	}

}

$userModel = $container->getService('userModel');
$user = $container->getByType(\Nette\Security\User::class);

(new AuthenticatorTest($container->getService('cms.authenticator'), $userModel, $user))->run();
