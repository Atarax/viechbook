<?php
/**
 * Created by PhpStorm.
 * User: atarax
 * Date: 4/27/14
 * Time: 12:43 AM
 *
 */
namespace MSpace\Model;
use Phalcon\Mvc\Model;

/**
 * Class ConversationsUsers
 * @package MSpace\Model
 *
 * @property mixed user_id
 * @property mixed conversation_id
 *
 */
class ConversationsUsers extends ModelBase {
	public function initialize() {
		parent::initialize();
	}

	/**
	 * @return int
	 */
	public function getConversation_id() {
		return $this->conversation_id;
	}

	/**
	 * @return int
	 */
	public function getUser_id() {
		return $this->user_id;
	}

	/**
	 * @param $id
	 */
	public function setConversation_id($id) {
		$this->conversation_id = $id;
	}

	/**
	 * @param $id
	 */
	public function setUser_id($id) {
		$this->user_id = $id;
	}
}