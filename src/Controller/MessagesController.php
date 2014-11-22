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
class MessagesController extends AppController {

    public function index() {}

    public function view($id = null) {
            /*
                $this->Message->id = $id;
                if (!$this->Message->exists()) {
                    throw new NotFoundException(__('Invalid message id given!'));
                }
                $this->set('message', $this->Message->read(null, $id));
            }

            /**
             * gets messages conversation by id
             */
    }
    public function getByConversation($conversationId = null) {
        $messages = TableRegistry::get('Messages')->findAllByConversation_id($conversationId)->contain( ['Users'] )->all();

        die( json_encode( $messages ) );
    }
}