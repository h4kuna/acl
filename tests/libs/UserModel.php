<?php

namespace h4kuna\Acl\Test;

use h4kuna\Acl\Security;

class UserModel extends Security\AuthenticatorFacadeAbstract
{

	private $users = [
		1 => [
			'id' => 1,
			'name' => 'Joe',
			'surname' => 'Doe',
			'block' => FALSE,
			'password' => '$2y$10$79DxQGPherAnaAubRPuLk.p3U6.lMhxIkQlIkQHAEKfNLYd4slhiO', // 123456
			'login_count' => 1
		],
		2 => [
			'id' => 2,
			'name' => 'alfred',
			'surname' => 'green',
			'block' => TRUE,
			'password' => '$2y$10$75NNlndNLdbalnEfN1MSF.Eh7uyb7/mPKkGb2Tbh.F44P2EkLCZFK', // password
			'login_count' => 0
		],
		3 => [
			'id' => 3,
			'name' => 'Masrer',
			'surname' => 'Blaster',
			'block' => FALSE,
			'password' => '$2y$10$79DxQGPherAnaAubRPuLk.p3U6.lMhxIkQlIkQHAEKfNLYd4slhiO', // 123456
			'login_count' => 0
		],
	];

	public function fetchUserById($id)
	{
		if (isset($this->users[$id])) {
			return $this->users[$id];
		}
		return NULL;
	}

	public function fetchUserByUsername($username)
	{
		foreach ($this->users as $data) {
			if ($data['name'] === $username) {
				return $data;
			}
		}
		return NULL;
	}

	public function createAuthenticatorStructure($data)
	{
		if (!$data) {
			return new Security\AuthenticatorStructure(0);
		}
		$struct = new Security\AuthenticatorStructure($data['id']);

		if (isset($data['password'])) {
			$struct->setPassword($data['password']);
		}

		$struct->setBlocked($data['block']);
		unset($data['password'], $data['block'], $data['id']);

		$struct->setData($data);
		return $struct;
	}

	public function loginSuccess(Security\User $user, $method)
	{
		if ($user->getId() == 1) {
			++$this->users[$user->getId()]['login_count'];
		}
	}

	/* NOT IN INTERFACE ***************************************************** */

	public function blockUser($userId)
	{
		$this->users[$userId]['block'] = TRUE;
	}

}
