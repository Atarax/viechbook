<?php

namespace Viechbook\Plugin;

use Phalcon\Acl\Adapter\Memory;
use Phalcon\Acl\Resource;
use Phalcon\Acl\Role;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use	Phalcon\Acl;

class Security extends PluginBase
{

	public function beforeDispatch(Event $event, Dispatcher $dispatcher){}

	public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher) {

		//Check whether the "auth" variable exists in session to define the active role
		$auth = $this->session->get('auth');
		if (!$auth) {
			$role = 'Guests';
		} else {
			$role = 'Users';
		}

		//Take the active controller/action from the dispatcher
		$controller = $dispatcher->getControllerName();
		$action = $dispatcher->getActionName();

		//Obtain the ACL list
		$acl = $this->getAcl();

		//Check if the Role have access to the controller (resource)
		$allowed = $acl->isAllowed($role, $controller, $action);
		if ($allowed != Acl::ALLOW) {

			//If he doesn't have access forward him to the index controller
			$this->flash->error("You don't have access to this module");
			$dispatcher->forward(
				array(
					'controller' => 'session',
					'action' => 'login'
				)
			);

			//Returning "false" we tell to the dispatcher to stop the current operation
			return false;
		}

	}

	public function getAcl() {
		//Create the ACL
		$acl = new Memory();

		//The default action is DENY access
		$acl->setDefaultAction(Acl::DENY);

		//Register two roles, Users is registered users
		//and guests are users without a defined identity
		$roles = array(
			'users' => new Role('Users'),
			'guests' => new Role('Guests')
		);
		foreach ($roles as $role) {
			$acl->addRole($role);
		}

		//Private area resources (backend)
		$privateResources = array(
			'pages' => array('users'),
			'messages' => array('get_by_conversation'),
			'users' => array('edit', 'list_all', 'get_notifications', 'profile', 'messages', 'add'),
			'conversations' => array('list_all', 'get_participants', 'add_message','add_message_by_receiver', 'clear_notifications'),
		);
		foreach ($privateResources as $resource => $actions) {
			$acl->addResource(new Resource($resource), $actions);

			foreach ($actions as $action) {
				$acl->allow('Users', $resource, $action);
			}
		}

		//Public area resources (frontend)
		$publicResources = array(
			'index' => array('index'),
			'index' => array('reset_password'),
			'session' => array('login', 'logout', 'start')
		);

		foreach ($publicResources as $resource => $actions) {
			$acl->addResource(new Resource($resource), $actions);
		}

		//Grant access to public areas to both users and guests
		foreach ($roles as $role) {
			foreach ($publicResources as $resource => $actions) {
				foreach ($actions as $action) {
					$acl->allow($role->getName(), $resource, $action);
				}
			}
		}

		return $acl;
	}
}