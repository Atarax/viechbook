<?php

namespace MSpace\Controller;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 16.12.14
 * Time: 00:24
 */

class PagesController extends ControllerBase {

    public function status() {
        //require_once(APP . 'Vendor' . DS . 'teamspeak' . DS . 'TeamSpeak3.php');
        //var_dump(App::path('vendors'));
        //die( );
        //App::import('Vendor', 'Teamspeak/Helper/Profiler');
        App::import('Vendor', 'Teamspeak/TeamSpeak3');

        TeamSpeak3::init();
        $serverIp = gethostbyname('MSpace.dev');
        $user = "monitor";
        $pass = "KCGp2adC";

        try {
            $ts3VirtualServer = TeamSpeak3::factory('serverquery://' . $user . ':' . $pass . '@' . $serverIp . ':10011/?server_port=9987');
            $this->set("serverInfo", $ts3VirtualServer->getInfo(true));
        }
        catch(Exception $e) {
            $this->Session->setFlash($e->getMessage());
        }
    }

    public function minecraftLivemap() {

    }

    public function balance() {}

    public function minutes() {}

    public function usersAction() {
	}

    public function events() {}
}
