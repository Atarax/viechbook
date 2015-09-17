<?php

namespace Viechbook\Controller;

use Phalcon\Mvc\Controller;
use Viechbook\Model\Users;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 17.12.14
 * Time: 12:31
 *
 * @property \Phalcon\Http\Request request
 * @property \Phalcon\Http\Response response
 * @property \Phalcon\Session session
 * @property \Phalcon\Security security
 * @property \Phalcon\Flash\Direct flash
 * @property \Phalcon\Mvc\Dispatcher dispatcher
 * @property \Phalcon\View view
 *
 */

class ControllerBase extends Controller{
	/** @var Users $currentUser */
	public $currentUser;
	public $db;

	protected $_isJsonResponse = false;

	// Call this func to set json response enabled
	public function setJsonResponse() {
		$this->view->disable();

		$this->_isJsonResponse = true;
		$this->response->setContentType('application/json', 'UTF-8');
	}

	// After route executed event
	public function afterExecuteRoute(\Phalcon\Mvc\Dispatcher $dispatcher) {
		if ($this->_isJsonResponse) {
			$data = $dispatcher->getReturnedValue();
			if (is_array($data)) {
				$data = json_encode($data);
			}

			$this->response->setContent($data);
			$this->response->send();
		}

		/** track user-activity */
		if(!$this->currentUser->wasActive()) {
			$this->currentUser->trackActivity();
		}
	}

	public function beforeExecuteRoute($dispatcher) {
		/** set the db */
		$this->db = $this->getDI()->get('db');

		/** set current user */
		$currentUser = $this->session->get('auth');

		/** set in controller-level */
		$this->currentUser = Users::findFirst($currentUser['id']);

		$this->view->setVar('currentUser', $currentUser);

		/** css resources */
		$this->assets
			->addCss('/vendor/bootstrap/css/bootstrap.min.css')
			->addCss('/css/emoji.css')
			->addCss('/css/viechbook.css');

		/** js resources */
		$this->assets
			->addJs('https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js', true)
			->addJs('/vendor/bootstrap/js/bootstrap.min.js')
			->addJs('https://autobahn.s3.amazonaws.com/js/autobahn.min.js')
			->addJs('/vendor/typeahead/js/bootstrap3-typeahead.min.js')
			->addJS('/js/viechbookConnector.js')
			->addJS('/js/viechbookChat.js')
			->addJS('/js/emojiMapping.js');

		if(VIECHBOOK_ENV == 'LIVE') {
			$this->assets->addJs('/js/gasnippet.js');
		}
	}
}