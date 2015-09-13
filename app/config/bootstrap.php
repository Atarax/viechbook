<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 17.12.14
 * Time: 12:08
 */

use Phalcon\Loader;
use Viechbook\Plugin\Security;


try {
	/** load composer autoloader */
	require_once(__DIR__ . '/../../vendor/autoload.php');

	/** load environment constants */
	require_once('environment.php');

	/**  read the configuration */
	$config = new Phalcon\Config\Adapter\Ini('../app/config/config.ini');

	//Create a DI
	$di = new Phalcon\DI\FactoryDefault();

	$di->set('router', function() {
		$router = new Phalcon\Mvc\Router();
		$router->setDefaultNamespace('Viechbook\Controller');
		return $router;
	});

	$di->set('flash', function (){
		$flash = new \Phalcon\Flash\Direct([
			//tie in with twitter bootstrap classes
			'error'     => 'alert alert-danger',
			'success'   => 'alert alert-success',
			'notice'    => 'alert alert-info',
			'warning'   => 'alert alert-warning'
		]);
		return $flash;
	});

	//Setup the view component
	$di->set('view', function(){
		$view = new \Phalcon\Mvc\View();
		$view->setViewsDir('../app/views/');
		return $view;
	});

	//Setup a base URI so that all generated URIs include the "tutorial" folder
	$di->set('url', function(){
		$url = new \Phalcon\Mvc\Url();
		$url->setBaseUri('/');
		return $url;
	});

	//Start the session the first time a component requests the session service
	$di->set('session', function() {
		$session = new Phalcon\Session\Adapter\Files();
		$session->start();
		return $session;
	});

	// Database connection is created based on parameters defined in the configuration file
	$di->set('db', function() use ($config) {
		return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
			"host" => $config->database->host,
			"username" => $config->database->username,
			"password" => $config->database->password,
			"dbname" => $config->database->name,
			"options" => [
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
			]
		));
	});

	$dispatcher = null;

	// set custom dispatcher for security-handling
	$di->set('dispatcher',
		/**
		 * @return \Phalcon\Mvc\Dispatcher
		 */
		function() use ($di) {

			//Obtain the standard eventsManager from the DI
			$eventsManager = $di->getShared('eventsManager');

			//Instantiate the Security plugin
			$security = new Security($di);

			//Listen for events produced in the dispatcher using the Security plugin
			$eventsManager->attach('dispatch', $security);

			$dispatcher = new Phalcon\Mvc\Dispatcher();

			//Bind the EventsManager to the Dispatcher
			$dispatcher->setEventsManager($eventsManager);

			return $dispatcher;
		}
	);

	//Handle the request
	$application = new \Phalcon\Mvc\Application($di);

	echo $application->handle()->getContent();

} catch(\Phalcon\Exception $e) {

	if(VIECHBOOK_ENV != 'LIVE') {
		echo "PhalconException: ", $e->getMessage();
	}

	file_put_contents('../logs/phalcon_errors.log', $e->getMessage() . "\n", FILE_APPEND );
}