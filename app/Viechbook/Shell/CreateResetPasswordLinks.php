<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 22.11.14
 * Time: 19:38
 */
namespace Viechbook\Shell;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\ZMQ\Context;
use Viechbook\Library\TheViech;
use Viechbook\Model\Users;

require 'vendor/autoload.php';
require 'app/config/bootstrap.php';


$users = Users::find();

foreach($users as $user) {
	echo $user->getEmail()." ".sha1(time())."\n";
}