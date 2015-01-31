<?php
namespace Viechbook\Controller;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 15.12.14
 * Time: 15:38
 */

class IndexController extends ControllerBase {
	public function indexAction() {
		// default index
		return $this->dispatcher->forward(array(
			'controller' => 'pages',
			'action' => 'users'
		));
	}
} 