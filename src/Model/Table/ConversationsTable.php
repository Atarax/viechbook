<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 21.11.14
 * Time: 23:05
 */
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * @property MessagesTable Messages
 * @property UsersTable Users UsersTable
 *
 */
class ConversationsTable extends Table {

    public function initialize(array $config) {
        $this->hasMany('Messages');
        $this->belongsToMany('Users');
    }
}