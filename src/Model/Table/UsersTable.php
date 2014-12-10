<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 21.11.14
 * Time: 21:33
 */

namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Class UsersTable
 * @package App\Model\Table
 *
 * @property NotificationsTable Notifications
 * @property MessagesTable Messages
 */
class UsersTable extends Table {

    public function initialize(array $config) {
        $this->belongsToMany('Conversations');
        $this->hasMany('Messages');
        $this->hasMany('Notifications');

		$this->hasOne('Picture', [
			'propertyName' => 'profilePicture'
		]);
	}

    public function validationDefault(Validator $validator) {
        return $validator
            ->notEmpty('username', 'A username is required')
            ->notEmpty('password', 'A password is required')
            ->notEmpty('email', 'An email is required');
    }
}