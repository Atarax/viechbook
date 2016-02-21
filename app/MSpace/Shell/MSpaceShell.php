<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 22.11.14
 * Time: 19:38
 */
namespace MSpace\Shell;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\Wamp\WampServer;
use Ratchet\WebSocket\WsServer;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\ZMQ\Context;
use MSpace\Library\TheMSpace;

require 'vendor/autoload.php';

class TheMSpaceShell {
    public function main() {
        $this->displayStartMessage();

        $loop   = Factory::create();
        $pusher = new TheMSpace();

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
            $asciiMSpace = file_get_contents("asciiMSpace.txt");
            echo $asciiMSpace;
        }
        catch(Exception $e){
            echo "Notice: MSpace-Art not found!\n";
        }

		echo "Starting The MSpace (Ratchet Server)...\n";

    }

}

$shell = new theMSpaceShell();
$shell->main();

