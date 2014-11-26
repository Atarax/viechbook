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
 */
class NotificationsTable extends Table {

    public function initialize(array $config) {
        $this->belongsTo('Users');
    }

    public function validationDefault(Validator $validator) {
        return $validator
            ->notEmpty('type', 'A type is required')
            ->notEmpty('user_id', 'A user is required');
    }
}