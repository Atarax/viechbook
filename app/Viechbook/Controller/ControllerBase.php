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
 * @property \Phalcon\Security security
 * @property \Phalcon\Flash\Direct flash
 * @property \Phalcon\Mvc\Dispatcher dispatcher
 * @property \Phalcon\View view
 *
 */

class ControllerBase extends Controller{
	public function beforeExecuteRoute($dispatcher) {
		/** set current user */
		$currentUser = $this->session->get('auth');
		$this->view->setVar('currentUser', $currentUser);

		/** css resources */
		$this->assets
			->addCss('/vendor/bootstrap/css/bootstrap.min.css')
			->addCss('/css/viechbook.css');

		/** js resources */
		$this->assets
			->addJs('https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js', true)
			->addJs('/vendor/bootstrap/js/bootstrap.min.js')
			->addJs('https://autobahn.s3.amazonaws.com/js/autobahn.min.js')
			->addJs('/vendor/typeahead/js/bootstrap3-typeahead.min.js');

		if(VIECHBOOK_ENV == 'LIVE') {
			$this->assets->addJs('/js/gasnippet.js');
		}
	}
}