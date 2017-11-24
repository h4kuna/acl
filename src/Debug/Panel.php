<?php

namespace h4kuna\Acl\Debug;

use h4kuna\Acl\Security;
use Tracy;

class Panel implements Tracy\IBarPanel
{

	/** @var Security\Authenticator */
	private $authenticator;

	/** @var Security\Permission|NULL */
	private $permission;

	public function __construct(Security\Authenticator $authenticator, Security\Permission $permission = null)
	{
		$this->authenticator = $authenticator;
		$this->permission = $permission;
	}

	public function getTab()
	{
		return file_get_contents(__DIR__ . '/tab.phtml');
	}

	public function getPanel()
	{
		$authenticatorMethods = $this->getAuthenticationMethods();
		$authorizatorMethods = $this->getAuthorizatorMethods();

		ob_start(function () {
		});
		require __DIR__ . '/panel.phtml';
		return ob_get_clean();
	}

	private function getAuthenticationMethods()
	{
		return $this->filterMethods(new \ReflectionClass($this->authenticator), 'loginBy');
	}

	private function getAuthorizatorMethods()
	{
		if ($this->permission === null) {
			return null;
		}
		$methods = $this->filterMethods(new \ReflectionClass($this->permission), 'resource');
		foreach ($methods as &$method) {
			$method['user'] = '$user->isAllowed' . $this->userAllowedParameters($method['sufix'], $method['parameters']);
		}
		unset($method);
		return $methods;
	}

	private function filterMethods(\ReflectionClass $class, $prefix)
	{
		$methods = [];
		foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
			/* @var $method \ReflectionMethod */
			if (!preg_match('~^' . $prefix . '(.*)~', $method->name, $find)) {
				continue;
			}

			$methods[] = [
				'file' => $class->getFileName(),
				'line' => $method->getStartLine(),
				'prototype' => $method->name . $this->filterParameters($method->getParameters()),
				'sufix' => lcfirst($find[1]),
				'parameters' => $method->getParameters(),
			];
		}

		return $methods;
	}

	private function userAllowedParameters($resource, array $parameters)
	{
		$param = new \stdClass();
		$param->value = $resource;
		$parameters[0] = $param;
		return $this->filterParameters($parameters);
	}

	private function filterParameters(array $parameters)
	{
		$out = '';
		foreach ($parameters as $param) {
			/* @var $param \ReflectionParameter */
			if ($out !== '') {
				$out .= ', ';
			}
			$out .= isset($param->name) ? ('$' . $param->name) : "'{$param->value}'";
		}
		return '(' . $out . ')';
	}

}
