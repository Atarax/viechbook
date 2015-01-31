<?php
/**
 * Created by PhpStorm.
 * User: atarax
 * Date: 4/27/14
 * Time: 12:43 AM
 *
 */
namespace Viechbook\Model;
use Phalcon\Exception;
use Phalcon\Mvc\Model\ValidationFailed;

/**
 * @property boolean isGroup
 */
class Conversations extends ModelBase {
	public $id;

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
			'Viechbook\Model\ConversationsUsers',
			'conversation_id',
			'user_id',
			'Viechbook\Model\Users',
			'id',
			['alias' => 'users']
		);

		$this->hasMany(
			'id',
			'Viechbook\Model\Messages',
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
	 * @return \Viechbook\Model\Messages[]
	 */
	public function getUserMessages($parameters=null) {
		return $this->getRelated('messages', $parameters);
	}

	/**
	 * @return \Viechbook\Model\Users[]
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