<?php
/**
 * Created by PhpStorm.
 * User: atarax
 * Date: 4/27/14
 * Time: 12:43 AM
 *
 */
namespace MSpace\Model;
use Phalcon\Exception;
use Phalcon\Mvc\Model\ValidationFailed;

/**
 * @property boolean isGroup
 */
class Conversations extends ModelBase {

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

		$this->hasManyToMany(
			'id',
			'MSpace\Model\ConversationsUsers',
			'conversation_id',
			'user_id',
			'MSpace\Model\Users',
			'id',
			['alias' => 'users']
		);

		$this->hasMany(
			'id',
			'MSpace\Model\Messages',
			'conversation_id',
			['alias' => 'messages']
		);
	}

	/**
	 * @return boolean
	 */
	public function getIsGroup() {
		return $this->isGroup;
	}

	/**
	 * @param boolean $isGroup
	 */
	public function setIsGroup($isGroup) {
		$this->isGroup = $isGroup;
	}

	/**
	 * @return \MSpace\Model\Messages[]
	 */
	public function getUserMessages($parameters=null) {
		return $this->getRelated('messages', $parameters);
	}

	/**
	 * @return \MSpace\Model\Users[]
	 */
	public function getUsers($parameters=null) {
		return $this->getRelated('users', $parameters);
	}

	/**
	 * check if the save went dine
	 *
	 * @throws Exception
	 */
	public function afterSave() {
		$errors = $this->getMessages();

		$message = 'Model method save of class: \'' . get_class($this) . '\' failed due to followin reasons: ';
		$message .= implode(",\n", $errors);

		if( !empty($errors) ) {
			throw new ValidationFailed( $message );
		}
	}

}