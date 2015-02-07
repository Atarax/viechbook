<?php

namespace Viechbook\Controller;
use Phalcon\Exception;
use Phalcon\Mvc\Model\Query;
use Phalcon\Mvc\View;
use Viechbook\Model\SecurityTokens;
use Viechbook\Model\Users;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 16.12.14
 * Time: 00:24
 */

class UsersController extends ControllerBase {

    public function index() {}

    public function addAction($token) {
        if ($this->request->isPost()) {
			/** @var SecurityTokens $token */
			$token = SecurityTokens::findFirst([
				'token = :token:',
				'bind' => ['token' => $token]
			]);

			/**
			 * Only users with token are allowed to register
			 */
			if($token && $token->getPayload() == 'signup') {

				$user = new Users();

				$password = $this->security->hash( $this->request->getPost('password') );

				$user->setEmail( $this->request->getPost('email') );
				$user->setUsername( $this->request->getPost('username') );
				$user->setPassword( $password );

				$user->save();

				$errors = $user->getMessages();

				if( empty($errors) ) {
					/** success! */
					$token->delete();

					$this->flash->success( 'Welcome to Viechbook' );
				}
			}

			$this->dispatcher->forward(array(
				'controller' => 'session',
				'action' => 'login'
			));

			return;
        }

		$this->view->setVar('token', $token);
    }

	public function get_password_reset_linksAction() {
		$currentUser = $this->session->get('auth');

		if($currentUser['id'] != 1) {
			die();
		}
		$users = Users::find();

		/** @var Users $user */
		foreach($users as $user) {
			$token = new SecurityTokens();
			$token->setPayload($user->getId());
			$token->save();

			echo '<a href="https://viechbook.com/users/reset_password/' . $token->getToken() . '">' . $user->getUsername() . '</a><br>';
		}

		die();
	}

    public function editAction($id = null) {
		/** @var Users $user */
		$user = Users::findFirst($id);
		$auth = $this->session->get('auth');

		if($id != $auth['id']) {
			throw new Exception(__('You are not allowed to change another users profile!'));
		}
		if( !is_object($user) ) {
			throw new Exception(__('User not found or unproper userid given!'));
		}



		if( $this->request->isPost() ) {

			$user->setEmail( $this->request->getPost('email') );
			$user->save();

			$errors = $user->getMessages();

			if( !empty($errors) ) {
				$this->flash->error( $errors );
			}
			else {
				$this->flash->success('Successfully updated your profile!');
				$this->dispatcher->forward([
					'action' => 'profile',
					'params' => [$id]
				]);
			}
		}

        $this->view->setVar('user', $user);
    }

    public function messagesAction() {}

	public function reset_passwordAction($tokenValue) {
		// Instantiate the Query
		$token = SecurityTokens::findFirst([
			'token = :token:',
			'bind' => ['token' => $tokenValue]
		]);

		if( !$token ) {
			$this->flash->error('Invalid token, sorry!');

			$this->dispatcher->forward([
				'controller' => 'index',
				'action' => 'index'
			]);

			return;
		}

		$userId = intval($token->getPayload());
		$user = Users::findFirst($userId);

		if( $this->request->isPost() ) {

			$password = $this->security->hash( $this->request->getPost('password') );

			$user->setPassword($password);
			$user->save();

			$token->delete();

			$this->flash->success('Successfully updated password!');

			$this->dispatcher->forward([
				'controller' => 'session',
				'action' => 'login'
			]);
		}

		$this->view->setRenderLevel(View::LEVEL_LAYOUT);
		$this->view->setVar('token', $tokenValue);
		$this->view->setVar('user', $user);
	}

    public function profileAction($id) {
        $user = Users::findFirst($id);

        if( !is_object($user) ) {
            throw new Exception(__('User not found or unproper userid given!'));
        }

        $this->view->setVar('user', $user->toArray());
    }

    public function list_allAction() {
        $users = Users::find();

        die( json_encode( ['data' => $users->toArray()]) );
    }

    public function get_notificationsAction() {
        /** @var Users $user */
        $user = Users::findFirst($this->session->get('auth')['id'] );

        die( json_encode( $user->getNotifications()->toArray() ) );
    }
}
