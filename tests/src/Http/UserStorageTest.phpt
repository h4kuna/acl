<?php

namespace h4kuna\Acl\Http;

use h4kuna\Acl\Security,
	Nette,
	Tester\Assert;

$container = require __DIR__ . '/../../bootstrap.php';

class UserStorageTest extends \Tester\TestCase
{

	/** @var Security\SynchronizeIdentity */
	private $synchronizeIdentity;

	/** @var Nette\Http\Session */
	private $session;

	public function __construct(Security\SynchronizeIdentity $synchronizeIdentity, Nette\Http\Session $session)
	{
		$this->synchronizeIdentity = $synchronizeIdentity;
		$this->session = $session;
	}

	public function testBasic()
	{
		$userStorage = new UserStorage($this->session, $this->synchronizeIdentity);
		$userStorage->setIdentity(new Security\Identity(new Security\AuthenticatorStructure(1))); // unserialize
		Assert::same('Joe', $userStorage->getIdentity()->name);
	}

}

$synchronizeIdentity = $container->getService('cms.synchronizeIdentity');
$session = $container->getService('session.session');

(new UserStorageTest($synchronizeIdentity, $session))->run();
