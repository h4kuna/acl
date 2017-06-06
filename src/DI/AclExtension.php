<?php

namespace h4kuna\Acl\DI;

use h4kuna\Acl\Http,
	h4kuna\Acl\Security,
	Nette\DI as NDI;

class AclExtension extends NDI\CompilerExtension
{

	public $defaults = [
		'storage' => '@cache.storage',
		'debugMode' => '%debugMode%'
	];

	/** @var array */
	private $expandConfig;

	public function loadConfiguration()
	{
		$this->expandConfig = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		// authenticator
		$builder->addDefinition($this->prefix('authenticator'))
			->setClass(Security\Authenticator::class);

		// synchronizeIdentity
		$synchronizeIdentity = $builder->addDefinition($this->prefix('synchronizeIdentity'))
			->setClass(Security\SynchronizeIdentity::class, [$this->expandConfig['storage']]);

		$userStorage = $builder->getDefinition('security.userStorage')
			->setClass(Http\UserStorage::class, [$builder->getDefinition('session.session'), $synchronizeIdentity]);

		// user
		$builder->getDefinition('security.user')
			->setClass(Security\User::class, [$userStorage, NULL, NULL]);

		// identityFactory
		$builder->addDefinition($this->prefix('identityFactory'))
			->setClass(Security\IdentityFactory::class);
	}

	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();
		$permission = $builder->getByType(Security\PermissionInterface::class);
		if ($permission !== NULL) {
			$authorizator = $builder->addDefinition($this->prefix('authorizator'))
				->setClass(Security\Authorizator::class, ['@' . $permission]);

			$user = $builder->getDefinition('security.user');
			$user->getFactory()->arguments[2] = $authorizator;
		}

		$builder->addDefinition($this->prefix('panel'))
			->setClass('h4kuna\Acl\Debug\Panel');
	}

	public function afterCompile(\Nette\PhpGenerator\ClassType $class)
	{
		$initialize = $class->getMethod('initialize');
		if (class_exists('Tracy\Debugger')) {
			$initialize->addBody('if ($this->parameters[\'debugMode\'] && ?) { Tracy\Debugger::getBar()->addPanel($this->getService(?));}', [$this->expandConfig['debugMode'], $this->prefix('panel')]);
		}
	}

}
