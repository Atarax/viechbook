<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 21.11.14
 * Time: 23:05
 */
namespace App\Model\Table;

use Cake\Validation\Validator;
use Cake\ORM\Table;

/**
 * @property MessagesTable Messages
 */
class PicturesTable extends Table {

	public function initialize(array $config) {
		$this->belongsTo('Users');
	}

	public function validationDefault(Validator $validator) {
		/*
		return $validator
			->notEmpty('user_id', 'A user is required');
		*/

		/** not sure what to validate :P */
		return $validator;
	}

}