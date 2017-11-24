<?php

namespace h4kuna\Acl\Security;

use h4kuna\Acl\Test,
	h4kuna\Acl\Http,
	Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

class GlobalIdentityTest extends \Tester\TestCase
{

	/** @var GlobalIdentity */
	private $globalIdentity;

	/** @var Authenticator */
	private $authenticator;

	/** @var Test\UserModel */
	private $userModel;

	public function __construct(GlobalIdentity $globalIdentity, Authenticator $authenticator, Test\UserModel $userModel)
	{
		$this->globalIdentity = $globalIdentity;
		$this->authenticator = $authenticator;
		$this->userModel = $userModel;
	}

	public function testFoo()
	{
		$data = $this->globalIdentity->getData(3);
		Assert::same([
			'id' => 3,
			'name' => 'Masrer',
			'surname' => 'Blaster',
			'block' => false
		], $data->getData());

		$user = $this->authenticator->loginById(3);
		Assert::same($data, $user->getGlobalData());
	}

	public function testReloadIdentityBlockUser()
	{
		$userId = 3;
		$user = $this->authenticator->loginById($userId);
		Assert::true($user->isLoggedIn());
		$identity = $user->getIdentity();
		$this->userModel->blockUser($userId);
		$user->reloadIdentity();

		Assert::true($user->getGlobalData()->block);
	}

}

$synchronizeIdentity = $container->getService('cms.globalIdentity');
$authenticator = $container->getByType(Authenticator::class);
$userModel = $container->getByType(Test\UserModel::class);

(new GlobalIdentityTest($synchronizeIdentity, $authenticator, $userModel))->run();
