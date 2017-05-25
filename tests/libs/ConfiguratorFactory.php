<?php

namespace h4kuna\Acl\Test;

use Nette,
	Tracy;

class ConfiguratorFactory
{

	public static function create($tempDir, $libsDir)
	{
		date_default_timezone_set('Europe/Prague');
		$configurator = new Nette\Configurator();
		$configurator->setTempDirectory($tempDir);
		Tracy\Debugger::enable(Tracy\Debugger::DEVELOPMENT, $tempDir);
		$configurator->createRobotLoader()
			->addDirectory($libsDir)
			->register();

		return $configurator;
	}

}
