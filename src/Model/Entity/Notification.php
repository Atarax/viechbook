<?php
/**
 * Created by PhpStorm.
 * User: atarax
 * Date: 4/27/14
 * Time: 12:43 AM
 */
namespace App\Model\Entity;


use Cake\ORM\Entity;

/**
 * @property User user
 */
class Notification extends Entity {
    const TYPE_NEW_MESSAGE = 1;
    const TYPE_NOTIFICATION_CHANGED = 2;

}