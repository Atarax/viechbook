<?php
/**
 * Created by PhpStorm.
 * User: atarax
 * Date: 4/27/14
 * Time: 12:43 AM
 */
namespace Viechbook\Model;

/**
 * @property Users user
 */
class SecurityTokens extends ModelBase {
	const TYPE_NEW_MESSAGE = 1;
	const TYPE_NOTIFICATION_CHANGED = 2;

	public $token;
	public $payload;

	/**
	 * @return String
	 */
	public function getPayload()
	{
		return $this->payload;
	}

	/**
	 * @param String $payload
	 */
	public function setPayload($payload)
	{
		$this->payload = $payload;
	}

	/**
	 * @return String
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * @param String $token
	 */
	public function setToken($token) {
		$this->token = $token;
	}
}