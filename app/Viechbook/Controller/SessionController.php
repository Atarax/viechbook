<?php

namespace Viechbook\Controller;

use Phalcon\Mvc;
use Phalcon\Mvc\View;
use Viechbook\Model\Users;

/**
 */
class SessionController extends ControllerBase
{

	private function _registerSession($user) {
		$this->session->set('auth', array(
			'id' => $user->id,
			'username' => $user->username
		));
	}

	public function startAction() {
		if ($this->request->isPost()) {

			//Receiving the variables sent by POST
			$username = $this->request->getPost('username');
			$password = $this->request->getPost('password');

			$password = sha1($password);

			//Find the user in the database
			$user = Users::findFirst(array(
				"username = :username: AND password = :password: ",
				"bind" => array('username' => $username, 'password' => $password)
			));
			if ($user != false) {

				$this->_registerSession($user);

				$this->flash->success('Welcome ' . $user->username);

				//Forward to the 'invoices' controller if the user is valid
				return $this->dispatcher->forward(array(
					'controller' => 'index',
					'action' => 'index'
				));
			}

			$this->flash->error('Wrong email/password');
		}

		//Forward to the login form again
		$this->dispatcher->forward(array(
			'controller' => 'session',
			'action' => 'login'
		));

	}

	public function loginAction() {
		// Shows only the view related to the action
		$this->view->setRenderLevel(View::LEVEL_LAYOUT);
	}

	public function logoutAction() {
		$this->session->set('auth', null);

		$this->dispatcher->forward(array(
			'controller' => 'session',
			'action' => 'login'
		));
	}
}