<?php
/**
 * Created by PhpStorm.
 * User: atarax
 * Date: 4/27/14
 * Time: 12:43 AM
 */
namespace MSpace\Model;

/**
 * @property Users user
 */
class UserSettings extends ModelBase {
	public $user_id;
	public $openWindows;
	public $lastInteraction;
}