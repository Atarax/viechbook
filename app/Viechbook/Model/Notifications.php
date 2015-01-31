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
class Notifications extends ModelBase {
    const TYPE_NEW_MESSAGE = 1;
    const TYPE_NOTIFICATION_CHANGED = 2;

	public $user_id;
	public $content;
	public $type;

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return mixed
	 */
	public function getUserId() {
		return $this->user_id;
	}

	/**
	 * @param mixed $user_id
	 */
	public function setUserId($user_id) {
		$this->user_id = $user_id;
	}

	/**
	 * @return mixed
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param mixed $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}



	public function initialize() {
		parent::initialize();

		$this->belongsTo('user_id', 'Viechbook\Model\Users', 'id', ['alias' => 'user']);
		$this->belongsTo('conversation_id', 'Viechbook\Model\Conversations', 'id', ['alias' => 'conversation']);
	}
}