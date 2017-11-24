<?php

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . '/libs/ConfiguratorFactory.php';

$tempDir = __DIR__ . '/temp/' . getmypid();
\Nette\Utils\FileSystem::createDir($tempDir . '/sessions');

$configurator = h4kuna\Acl\Test\ConfiguratorFactory::create($tempDir, __DIR__ . '/libs/');
$configurator->addConfig(__DIR__ . '/config/config.neon');

Tester\Environment::setup();

return $configurator->createContainer();
