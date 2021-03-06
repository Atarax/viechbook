<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 17.12.14
 * Time: 11:42
 */
namespace MSpace\Model;

class Users extends ModelBase {
	public $id;
	public $username;
	public $email;
	public $password;

	public function initialize() {
		parent::initialize();

		$this->hasOne(
			'id',
			'MSpace\Model\UserSettings',
			'user_id',
			['alias' => 'settings']
		);

		$this->hasMany(
			'id',
			'MSpace\Model\Messages',
			'user_id',
			['alias' => 'messages']
		);

		$this->hasMany(
			'id',
			'MSpace\Model\Notifications',
			'user_id',
			['alias' => 'notifications']
		);

		$this->hasManyToMany(
			'id',
			'MSpace\Model\ConversationsUsers',
			'user_id',
			'conversation_id',
			'MSpace\Model\Conversations',
			'id',
			['alias' => 'conversations']
		);
	}

	/**
	 * @return mixed
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param mixed $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * @return String
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param String $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail($email) {
		$this->email = $email;
	}

	/**
	 * @return \MSpace\Model\Messages[]
	 */
	public function getUserMessages($parameters=null) {
		return $this->getRelated('messages', $parameters);
	}

	/**
	 * @return \MSpace\Model\Conversations[]
	 */
	/*
	public function getConversations($parameters=null) {
		return $this->getRelated('conversations', $parameters);
	}*/

	/**
	 * @return \MSpace\Model\Notifications[]
	 */
	public function getNotifications($parameters=null) {
		return $this->getRelated('notifications', $parameters);
	}

	/**  */
	public function getSettings($parameters=null) {
		$settings = $this->getRelated('settings', $parameters);

		if( empty($settings) ) {
			$settings = new UserSettings();
			$settings->user_id = $this->getId();
		}

		return $settings;
	}


	public function wasActive() {
		$actionInterval = date('Y-m-d H:i:s', time() - (60 * 3));

		$query = UserSettings::query()
			->where(
				'modified > :modified: AND user_id = :user_id:'
			)
			->bind([
				'modified' => $actionInterval,
				'user_id' => $this->id
			]);

		$result = $query->execute()->getFirst();

		return !empty($result);
	}
	/**
	 *
	 */
	public function trackActivity() {
		/** @var UserSettings $settings */
		$settings = $this->getSettings();
		$settings->setModified();
		$settings->save();
	}
}