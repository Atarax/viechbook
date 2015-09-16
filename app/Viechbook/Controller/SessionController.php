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

			$user = Users::findFirstByUsername($username);

			if($user) {
				if ($this->security->checkHash($password, $user->password)) {
					$this->_registerSession($user);

					$this->flash->success('Welcome ' . $user->getUsername());

					$this->response->redirect();
					$this->response->send();

					$this->dispatcher->forward(array(
						'controller' => 'index',
						'action' => 'index'
					));

					return;
				}
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

		$this->response->redirect();
		$this->response->send();

		return;
	}
}