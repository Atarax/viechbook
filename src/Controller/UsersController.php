<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Model\Table\UsersTable;
use Cake\Auth\PasswordHasherFactory;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @property UsersTable Users
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class UsersController extends AppController {

    public function index() {}

    public function add() {
        if ($this->request->is('post')) {
            $user = $this->Users->newEntity($this->request->data);

            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
    }


    public function edit($id = null) {
		$usersTable = TableRegistry::get('Users');
		$user = $usersTable->findById($id)->first();

		if($id != $this->Auth->user()['id']) {
            throw new Exception(__('You are not allowed to change another users profile!'));
        }

        if( !is_object($user) ) {
            throw new Exception(__('User not found or unproper userid given!'));
        }

        if( $this->request->is('post') || $this->request->is('put') ) {
			$usersTable->patchEntity($user, $this->request->data);

            if ($usersTable->save($user)) {
                $this->Flash->success(__('Your profile has been updated!'));
                return $this->redirect(array('controller' => 'pages', 'action' => 'users'));
            }
            $this->Session->Flash->error(__('The user could not be saved. Please, try again.'));
        }

        $this->set('user', $user);
    }

    public function delete($id = null) {
        $this->request->onlyAllow('post');

        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Session->setFlash(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('User was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }

    public function login() {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if($user) {
                $this->Auth->setUser($user);
                return $this->redirect( $this->redirect('/pages/users') );
            }
            $this->Flash->error(__('Invalid username or password, try again'));
        }

       $this->layout = false;
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }


    public function messages() {

    }

    public function profile($id) {
        $user = TableRegistry::get('Users')->findById($id)->first();

        if( !is_object($user) ) {
            throw new Exception(__('User not found or unproper userid given!'));
        }

        $this->set('user', $user);
    }

    public function listAll() {
        $users = TableRegistry::get('Users');
        $query = $users->find();
        $query->select(['id', 'username', 'email']);
        die( json_encode( ['data' => $query->all()->toArray()]) );
    }

    public function getNotifications() {
        /** @var UsersTable $users */
        $users = TableRegistry::get('Users');
        $user = $users->findById($this->Auth->user()['id'] )->contain( ['Notifications'] )->first();

        die( json_encode( $user->notifications ) );
    }
}
