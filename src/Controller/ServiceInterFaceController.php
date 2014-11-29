<?php
namespace App\Controller;
use App\Model\Table\ConversationsTable;
use Cake\ORM\TableRegistry;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 4/27/14
 * Time: 12:59 AM
 * @property ConversationsTable Conversations
 */
class ServiceInterFaceController extends AppController {

    public function getServices() {
        $servicesMessage = new \stdClass();
        $servicesMessage->services = [

        ];
    }
}