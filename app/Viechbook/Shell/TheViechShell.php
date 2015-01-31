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

require 'vendor/autoload.php';

class TheViechShell {
    public function main() {
        $this->displayStartMessage();

        $loop   = Factory::create();
        $pusher = new TheViech();

        // Listen for the web server to make a ZeroMQ push after an ajax request
        $context = new Context($loop);

        $pull = $context->getSocket(\ZMQ::SOCKET_PULL);
        $pull->bind('tcp://127.0.0.1:5555'); // Binding to 127.0.0.1 means the only client that can connect is itself
        $pull->on('message', array($pusher, 'onBlogEntry'));

        // Set up our WebSocket server for clients wanting real-time updates
        $webSock = new Server($loop);
        $webSock->listen(8088, '0.0.0.0'); // Binding to 0.0.0.0 means remotes can connect
        new IoServer(
            new HttpServer(
                new WsServer(
                    new WampServer(
                        $pusher
                    )
                )
            ),
            $webSock
        );

        $loop->run();
    }

    private function displayStartMessage() {
        try{
            $asciiViech = file_get_contents("asciiViech.txt");
            echo $asciiViech;
        }
        catch(Exception $e){
            echo "Notice: Viech-Art not found!\n";
        }

		echo "Starting The Viech (Ratchet Server)...\n";

    }

}

$shell = new theViechShell();
$shell->main();

