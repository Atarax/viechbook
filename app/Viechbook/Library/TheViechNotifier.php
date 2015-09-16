<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 29.11.14
 * Time: 17:24
 */

namespace Viechbook\Library;


use ZMQContext;

class TheViechNotifier {
    private $socket;

    public function __construct() {
        $this->socket = $this->getZMQSocket();
    }

    public function notify($userId, $type, $message = null) {
        $notification = array(
            'category' => '' . $userId,
            'type' => $type,
            'data' => $message
        );

        $this->socket->send(json_encode($notification));
    }

    /**
     * @return mixed
     */
    private function getZMQSocket() {
        $context = new ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'new message pusher');
        $socket->connect("tcp://localhost:5555");

        return $socket;
    }
} 