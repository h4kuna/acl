<?php

namespace h4kuna\Acl\Test;

use h4kuna\Acl\Security;

class UserModel implements Security\AuthenticatorFacade
{

	private $users = [
		1 => [
			'id' => 1,
			'name' => 'Joe',
			'surname' => 'Doe',
			'block' => false,
			'password' => '$2y$10$79DxQGPherAnaAubRPuLk.p3U6.lMhxIkQlIkQHAEKfNLYd4slhiO', // 123456
			'login_count' => 1,
		],
		2 => [
			'id' => 2,
			'name' => 'alfred',
			'surname' => 'green',
			'block' => true,
			'password' => '$2y$10$75NNlndNLdbalnEfN1MSF.Eh7uyb7/mPKkGb2Tbh.F44P2EkLCZFK', // password
			'login_count' => 0,
		],
		3 => [
			'id' => 3,
			'name' => 'Masrer',
			'surname' => 'Blaster',
			'block' => false,
			'password' => '$2y$10$79DxQGPherAnaAubRPuLk.p3U6.lMhxIkQlIkQHAEKfNLYd4slhiO', // 123456
			'login_count' => 0,
		],
	];

	public function createAuthenticatorStructureByUsername($username)
	{
		$data = $this->fetch('name', $username);
		return $this->createAuthenticatorStructure($data);
	}

	public function createAuthenticatorStructureById($userId)
	{
		$data = $this->fetchUserById($userId);
		return $this->createAuthenticatorStructure($data);
	}

	public function loginSuccess(Security\User $user, $method)
	{
		if ($user->getId() == 1) {
			++$this->users[$user->getId()]['login_count'];
		}
	}

	public function createGlobalDataById($userId)
	{
		$data = $this->fetchUserById($userId);
		if (!$data) {
			return null;
		}
		unset($data['password'], $data['login_count']);
		return new Security\GlobalData($data);
	}

	/* NOT IN INTERFACE ***************************************************** */

	public function blockUser($userId)
	{
		$this->users[$userId]['block'] = true;
	}

	public function fetchUserById($userId)
	{
		return $this->fetch('id', $userId);
	}

	private function createAuthenticatorStructure($data)
	{
		if (!$data) {
			return new Security\AuthenticatorStructure(0);
		}
		return new Security\AuthenticatorStructure($data['id'], $data['block'], $data['password']);
	}

	private function fetch($column, $value)
	{
		foreach ($this->users as $id => $columns) {
			if ($columns[$column] == $value) {
				return $columns;
			}
		}
		return [];
	}
}
