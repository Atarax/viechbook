<?php

namespace Viechbook\Model;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Behavior\Timestampable;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 18.12.14
 * Time: 19:33
 *
 * @method mixed[] toArray()
 *
 */


class ModelBase extends Model {
	public $created;
	public $modified;

	/**
	 * @param null $data
	 * @param null $witelist
	 * @return bool|void
	 */
	public function save($data = null, $witelist = null) {
		$this->setModified();
		parent::save($data, $witelist);
	}

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

}