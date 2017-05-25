<?php

namespace h4kuna\Acl\Core\Security;

use h4kuna\Acl\Test,
	h4kuna\Acl\Core\Http,
	Tester\Assert;

$container = require __DIR__ . '/../../../bootstrap.php';

class SynchronizeIdentityTest extends \Tester\TestCase
{

	/** @var SynchronizeIdentity */
	private $synchronizeIdentity;

	/** @var Authenticator */
	private $authenticator;

	/** @var Test\UserModel */
	private $userModel;

	public function __construct(SynchronizeIdentity $synchronizeIdentity, Authenticator $authenticator, Test\UserModel $userModel)
	{
		$this->synchronizeIdentity = $synchronizeIdentity;
		$this->authenticator = $authenticator;
		$this->userModel = $userModel;
	}

	public function testFoo()
	{
		Assert::same([
			'name' => 'Masrer',
			'surname' => 'Blaster',
			'login_count' => 0
			], $data = $this->synchronizeIdentity->getIdentityData(3)->getData());

		$user = $this->authenticator->loginById(3);
		Assert::same($data, $user->getIdentity()->getData());
		Assert::type(Identity::class, $user->getIdentity());
	}

	public function testReloadIdentityBlockUser()
	{
		$userId = 3;
		$user = $this->authenticator->loginById($userId);
		Assert::true($user->isLoggedIn());
		$identity = $user->getIdentity();
		$this->userModel->blockUser($userId);
		$this->synchronizeIdentity->forceReloadUserIdentity($userId);
		$identity->setData(NULL);
		$user->getStorage()->setIdentity($identity);

		Assert::type(Identity::class, $user->getIdentity());
		Assert::false($user->isLoggedIn());
		Assert::same(Http\UserStorage::BLOCKED, $user->getLogoutReason());
	}

}

$synchronizeIdentity = $container->getService('cms.synchronizeIdentity');
$authenticator = $container->getByType(Authenticator::class);
$userModel = $container->getByType(Test\UserModel::class);


(new SynchronizeIdentityTest($synchronizeIdentity, $authenticator, $userModel))->run();
