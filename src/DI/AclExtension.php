<?php

namespace h4kuna\Acl\DI;

use h4kuna\Acl\Debug;
use h4kuna\Acl\Security;
use Nette\DI as NDI;
use Nette\PhpGenerator;

class AclExtension extends NDI\CompilerExtension
{

	private $defaults = [
		'storage' => '@cache.storage',
		'debugMode' => false,
	];

	/** @var array */
	private $expandConfig;

	public function __construct($debugMode = false)
	{
		$this->defaults['debugMode'] = $debugMode;
	}

	public function loadConfiguration()
	{
		$this->expandConfig = $this->config + $this->defaults;
		$builder = $this->getContainerBuilder();

		// user
		$user = $builder->getDefinition('security.user')
			->setFactory(Security\User::class);

		// identityFactory
		$identityFactory = $builder->addDefinition($this->prefix('identityFactory'))
			->setFactory(Security\IdentityFactory::class);

		// authenticator
		$builder->addDefinition($this->prefix('authenticator'))
			->setFactory(Security\Authenticator::class, [$user]);

		// globlaIdentity
		$builder->addDefinition($this->prefix('globalIdentity'))
			->setFactory(Security\GlobalIdentity::class, [$this->expandConfig['storage']]);
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$permission = $builder->getByType(Security\Permission::class);
		if ($permission !== null) {
			$authorizator = $builder->addDefinition($this->prefix('authorizator'))
				->setFactory(Security\Authorizator::class, ['@' . $permission]);

			$user = $builder->getDefinition('security.user');
			$user->getFactory()->arguments[2] = $authorizator;
		}

		$builder->addDefinition($this->prefix('panel'))
			->setFactory(Debug\Panel::class)
			->setAutowired(false);
	}

	public function afterCompile(PhpGenerator\ClassType $class)
	{
		$initialize = $class->getMethod('initialize');
		if (class_exists('Tracy\Debugger')) {
			$initialize->addBody('if ($this->parameters[\'debugMode\'] && ?) { Tracy\Debugger::getBar()->addPanel($this->getService(?));}', [
				$this->expandConfig['debugMode'],
				$this->prefix('panel'),
			]);
		}
	}

}
