<?php
namespace Viechbook\Model;
use Phalcon\Mvc\Model;

/**
 * Created by PhpStorm.
 * User: atarax
 * Date: 4/27/14
 * Time: 12:43 AM
 *
 */

class Messages extends ModelBase {
	public $content;
	public $user_id;
	public $conversation_id;
	public $read;

	/**
	 * @return mixed
	 */
	public function getRead(){
		return $this->read;
	}

	/**
	 * @param mixed $read
	 */
	public function setRead($read){
		$this->read = $read;
	}

	/**
	 * @return mixed
	 */
	public function getConversationId() {
		return $this->conversation_id;
	}

	/**
	 * @param mixed $conversation_id
	 */
	public function setConversationId($conversation_id) {
		$this->conversation_id = $conversation_id;
	}

	/**
	 * @return mixed
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param mixed $created
	 */
	public function setCreated($created) {
		$this->created = $created;
	}

	/**
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}

	/**
	 * @param int $user_id
	 */
	public function setUserId($user_id) {
		$this->user_id = $user_id;
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

	public function initialize() {
		parent::initialize();

		$this->hasMany('id', 'Messages', 'user_id');
	}

	/**
	 * @return String
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param String $content
	 */
	public function setContent($content) {
		$this->content = $content;
	}


}