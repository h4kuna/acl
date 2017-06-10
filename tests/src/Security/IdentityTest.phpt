<?php

namespace h4kuna\Acl\Security;

$container = require __DIR__ . '/../../bootstrap.php';

use Tester\Assert;

class IdentityTest extends \Tester\TestCase
{

	public function testUnserialize()
	{
		$serialize = 'C:28:"h4kuna\Acl\Security\Identity":4:{i:1;}';
		$identity = unserialize($serialize);
		Assert::same(1, $identity->getId());
		Assert::same(NULL, $identity->getData());
	}

	public function testSerialize()
	{
		$counter = new \h4kuna\Acl\Test\Counter();
		$structure = (new AuthenticatorStructure(1))->setData(['name' => 'Joe']);
		$identity = (new Identity($structure));
		$identity->onChangeIdentity[] = function() use ($counter) {
			++$counter->count;
		};
		Assert::same(1, $identity->getId());
		Assert::same('Joe', $identity->name);
		Assert::same('C:28:"h4kuna\Acl\Security\Identity":4:{i:1;}', serialize($identity));
		Assert::same(0, $counter->count);

		$identity->name = 'Doe';
		Assert::same('C:28:"h4kuna\Acl\Security\Identity":4:{i:1;}', serialize($identity));
		Assert::same(1, $counter->count);
	}

}

(new IdentityTest())->run();
