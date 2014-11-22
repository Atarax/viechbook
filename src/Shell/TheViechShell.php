<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 22.11.14
 * Time: 19:38
 */
namespace App\Shell;

use App\Model\TheViech;
use Cake\Console\Shell;
use Ratchet\Server\IoServer;


class TheViechShell extends Shell {
    public function main() {
        $this->out('

        ');
        $this->out('Starting The Viech (Ratchet Server)...');

        $server = IoServer::factory(
            new TheViech(),
            8080
        );

        $server->run();
    }
    public function onOpen(ConnectionInterface $conn) {
    }

    public function onMessage(ConnectionInterface $from, $msg) {
    }

    public function onClose(ConnectionInterface $conn) {
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    }


}
