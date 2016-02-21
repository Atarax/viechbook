<?php

namespace MSpace\Controller;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 16.12.14
 * Time: 00:24
 */

class ParvatiController extends ControllerBase {

	public function indexAction() {
		$this->view->setVar('data', 'hello');
	}
}
