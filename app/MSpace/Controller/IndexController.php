<?php
namespace MSpace\Controller;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 15.12.14
 * Time: 15:38
 */

class IndexController extends ControllerBase {
	/**
	 * @return void
	 */
	public function indexAction() {

		if( !$this->session->get('auth') ){

			$this->dispatcher->forward(array(
				'controller' => 'session',
				'action' => 'login'
			));
		}
	}
} 