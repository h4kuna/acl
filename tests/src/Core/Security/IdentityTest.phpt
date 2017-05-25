<?php

namespace h4kuna\Acl\Core\Security;

$container = require __DIR__ . '/../../../bootstrap.php';

use Tester\Assert;

class IdentityTest extends \Tester\TestCase
{

	public function testUnserialize()
	{
		$serialize = 'C:33:"h4kuna\Acl\Core\Security\Identity":4:{i:1;}';
		$identity = unserialize($serialize);
		Assert::same(1, $identity->getId());
		Assert::same(NULL, $identity->getData());
	}

	public function testSerialize()
	{
		$counter = new \h4kuna\Acl\Test\Counter();
		$structure = (new AuthenticatorStructure(1))->setData(['name' => 'Joe']);
		$identity = (new Identity(1))->setData($structure);
		$identity->onChangeIdentity[] = function() use ($counter) {
			++$counter->count;
		};
		Assert::same(1, $identity->getId());
		Assert::same('Joe', $identity->name);
		Assert::same('C:33:"h4kuna\Acl\Core\Security\Identity":4:{i:1;}', serialize($identity));
		Assert::same(0, $counter->count);

		$identity->name = 'Doe';
		Assert::same('C:33:"h4kuna\Acl\Core\Security\Identity":4:{i:1;}', serialize($identity));
		Assert::same(1, $counter->count);
	}

}

(new IdentityTest())->run();
