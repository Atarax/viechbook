<?php
/**
 * Created by JetBrains PhpStorm.
 * User: atarax
 * Date: 12/12/13
 * Time: 10:05 PM
 * To change this template use File | Settings | File Templates.
 */

// src/Model/Entity/User.php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * @property Conversation[] conversations
 */
class User extends Entity {
    // Make all fields mass assignable for now.
    protected $_accessible = ['*' => true];

	protected function _setPassword($password) {
		return (new DefaultPasswordHasher)->hash($password);
	}
	protected function _getPassword($password) {
		if(!$this->_new) {
			return '';
		}
		else {
			return $password;
		}
	}
}