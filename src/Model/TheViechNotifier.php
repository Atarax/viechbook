<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 29.11.14
 * Time: 17:24
 */

namespace App\Model;


use App\Controller\AppController;

class TheViechNotifier {
    private $socket;

    public function __construct() {
        $this->socket = AppController::getZMQSocket();
    }

    public function notify($userId, $type, $message = null) {
        $notification = array(
            'category' => '' . $userId,
            'type' => $type,
            'data' => $message
        );

        $this->socket->send(json_encode($notification));
    }
} 