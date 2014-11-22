<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\Utility\Inflector;
use Cake\View\Exception\MissingTemplateException;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

    public function status() {
        //require_once(APP . 'Vendor' . DS . 'teamspeak' . DS . 'TeamSpeak3.php');
        //var_dump(App::path('vendors'));
        //die( );
        //App::import('Vendor', 'Teamspeak/Helper/Profiler');
        App::import('Vendor', 'Teamspeak/TeamSpeak3');

        TeamSpeak3::init();
        $serverIp = gethostbyname('viechbook.dev');
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

    public function news() {}

    public function files() {}

    public function users() {}

    public function events() {}
}
