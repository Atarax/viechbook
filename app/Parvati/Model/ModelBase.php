<?php

namespace Parvati\Model;
use Phalcon\Exception;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Viechbook\Model\Exception\SaveFailed;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 18.12.14
 * Time: 19:33
 *
 * @method mixed[] toArray()
 *
 */


class ParvatiModelBase extends Model {
	public $id;
	public $created;
	public $modified;

	public function initialize() {
		$this->addBehavior(new Timestampable(
			array(
				'beforeValidationOnCreate' => array(
					'field' => 'created',
					'format' => 'Y-m-d H:i:s'
				)/*,
				'beforeCreate' => array(
					'field' => 'modified',
					'format' => 'Y-m-d'
				)*/
			)
		));

	}

	/**
	 * @return mixed
	 */
	public function getModified()  {
		return $this->modified;
	}

	/**
	 * @param mixed $modified
	 */
	public function setModified($modified = null) {
		if($modified == null) {
			$modified =  date('Y-m-d H:i:s');
		}
		$this->modified = $modified;
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
	 * check if the save went dine
	 *
	 * @throws Exception
	 */
	public function onValidationFails() {
		$errors = $this->getMessages();

		$message = 'Model method save of class: \'' . get_class($this) . '\' failed due to followin reasons: ';
		$message .= implode(",\n", $errors);

		if( !empty($errors) ) {
			throw new SaveFailed( $message );
		}
	}

	public function beforeValidation() {
		$this->setModified();
	}
}