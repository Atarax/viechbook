<?php

namespace Viechbook\Controller;
use Viechbook\Model\Users;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 16.12.14
 * Time: 00:24
 */

class UsersController extends ControllerBase {

    public function index() {}

    public function addAction() {
        if ($this->request->isPost()) {
            $user = new Users();

			$password = $this->security->hash( $this->request->getPost('password') );

			$user->setEmail( $this->request->getPost('email') );
			$user->setUsername( $this->request->getPost('username') );
			$user->setPassword( $password );
			$user->setModified();

			$success = $user->save();

            if($success) {
				$this->flash->success( 'The user has been saved' );

				$this->dispatcher->forward(array(
					'controller' => 'index',
					'action' => 'index'
				));

				return;
            }

			$this->flash->error( $user->getMessages() );
        }
    }


    public function editAction($id = null) {
		var_dump($_FILES);
		die();
		$user = Users::findFirst($id);
		$auth = $this->session->get('auth');

		if($id != $auth['id']) {
			throw new Exception(__('You are not allowed to change another users profile!'));
		}
		if( !is_object($user) ) {
			throw new Exception(__('User not found or unproper userid given!'));
		}

		/*
		if( $this->request->is('post') || $this->request->is('put') ) {
			$user
			$usersTable->patchEntity($user, $this->request->data);

			if ($usersTable->save($user)) {
				$this->Flash->success(__('Your profile has been updated!'));
				//return $this->redirect(array('controller' => 'pages', 'action' => 'users'));
			}
			$this->Flash->error(__('The user could not be saved. Please, try again.'));
		}
		*/
        $this->view->setVar('user', $user->toArray());
    }

    public function messagesAction() {}

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
