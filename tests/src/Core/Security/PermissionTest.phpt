<?php

namespace h4kuna\Acl\Core\Security;

namespace h4kuna\Acl\Core\Security;

use Tester\Assert;

$container = require __DIR__ . '/../../../bootstrap.php';

class PermissionTest extends \Tester\TestCase
{

	/** @var Authenticator */
	private $authenticator;

	public function __construct(Authenticator $authenticator)
	{
		$this->authenticator = $authenticator;
	}

	public function testGod()
	{
		$user = $this->authenticator->loginById(3);
		Assert::true($user->isAllowed('files'));
		Assert::true($user->isAllowed('files', 'checked'));
		Assert::true($user->isAllowed('files', 'foo'));
		Assert::true($user->isAllowed('file', 'read', 1));
		Assert::true($user->isAllowed('file', 'read', 2));
		Assert::true($user->isAllowed('file', 'delete', 1));
	}

	public function testNormalUser()
	{
		$user = $this->authenticator->loginById(1);
		Assert::true($user->isAllowed('files'));
		Assert::true($user->isAllowed('files', 'checked'));
		Assert::false($user->isAllowed('files', 'foo'));
		Assert::true($user->isAllowed('file', 'read', 1));
		Assert::false($user->isAllowed('file', 'read', 2));
		Assert::false($user->isAllowed('file', 'delete', 1));
	}

	public function testUserLogOut()
	{
		$user = $this->authenticator->loginById(1);
		$user->logout();
		Assert::true($user->isAllowed('files'));
		Assert::false($user->isAllowed('files', 'checked'));
		Assert::false($user->isAllowed('files', 'foo'));
		Assert::false($user->isAllowed('file', 'read', 1));
		Assert::false($user->isAllowed('file', 'read', 2));
		Assert::false($user->isAllowed('file', 'delete', 1));
	}

	/**
	 * @throws h4kuna\Acl\MethodIsNotImplementedException
	 */
	public function testFail()
	{
		$user = $this->authenticator->loginById(1);
		$user->isAllowed('foo');
	}

}

(new PermissionTest($container->getService('cms.authenticator')))->run();
