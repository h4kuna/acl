<?php

namespace h4kuna\Acl\Security;

use h4kuna\DataType\Immutable;

/**
 * @property-read mixed $id
 * @property-read bool $blocked
 * @property-read string $password
 * @property-read null|array|mixed $data
 */
class AuthenticatorStructure extends Immutable\Messenger
{

	public function __construct($id, $blocked = false, $password = '', $data = null)
	{
		parent::__construct([
			'id' => $id,
			'blocked' => (bool) $blocked,
			'password' => $password,
			'data' => $data,
		]);
	}

}
