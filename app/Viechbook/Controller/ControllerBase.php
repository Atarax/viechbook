<?php

namespace Viechbook\Controller;

use Phalcon\Mvc\Controller;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 17.12.14
 * Time: 12:31
 *
 * @property \Phalcon\Http\Request request
 * @property \Phalcon\Http\Response response
 * @property \Phalcon\Session session
 * @property \Phalcon\Flash\Direct flash
 * @property \Phalcon\Mvc\Dispatcher dispatcher
 * @property \Phalcon\View view
 *
 */

class ControllerBase extends Controller{
	public function beforeExecuteRoute($dispatcher) {
		$currentUser = $this->session->get('auth');

		$this->view->setVar('currentUser', $currentUser);
	}
} 