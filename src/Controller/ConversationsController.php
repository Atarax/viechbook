<?php
namespace App\Controller;
use App\Model\Entity\Conversation;
use App\Model\Entity\Message;
use App\Model\Entity\User;
use App\Model\Table\ConversationsTable;
use Cake\Network\Exception\NotFoundException;
use ZMQContext;

/**
 * Created by PhpStorm.
 * User: atarax
 * Date: 4/27/14
 * Time: 12:59 AM
 * @property ConversationsTable Conversations
 */
class ConversationsController extends AppController {

    /**
     * basically sends a message to a given receiver, taking into account that there might not exists a conversation yet
     * @param $receiverId
     */
    public function addMessageByReceiver($receiverId) {
        $currentUser = $this->Auth->user();

        /** @var User $sender */
        $sender = $this->Conversations->Users->findById($currentUser['id'])->contain( ['Conversations'] )->first();
        /** @var User $receiver */
        $receiver = $this->Conversations->Users->findById($receiverId)->contain( ['Conversations'] )->first();

        if( is_null($receiver) ) {
            throw new NotFoundException(__('Receiver for message not found! (maybe improper id given)'));
        }
        if( $this->request->is('post') ) {

            /**
             * check if we already have a conversation with the receiver
             */
            $commonConversation = $this->getCommonConversation($receiver, $sender);

            if( is_null($commonConversation) ) {
                $commonConversation = $this->createNewConversation($sender, $receiver);
            }

            $this->createMessage($commonConversation, $sender);

            $this->Flash->success(__('The message has been sent!'));
        }

        $this->set('receiver', $receiver);
    }

    /**
     * return all Conversations for current User as json
     */
    public function listAll() {
        $currentUser = $this->Auth->user();
        $currentUser = $this->Conversations->Users
            ->findById($currentUser['id'])
            ->contain( ['Conversations'] )
            ->order(['modified' => 'DESC'])
            ->first();

        $result = [];
        foreach($currentUser->get('conversations') as $conversation) {
            /** @var Conversation $conversation */
            $conversation = $this->Conversations
                ->findById( $conversation->get('id') )
                ->contain( ['Messages', 'Users'] )
                ->first();

            /** @var Message $lastMessage
             * get the message with the biggest id, resulting hopefully in newest message
             */
            $messageCount = count($conversation->messages);
            $lastMessage = $conversation->messages[ $messageCount - 1 ];
            $unreadMessageCount = 0;

            foreach($conversation->messages as $message) {
                if( !$message->get('read') ) {
                    $unreadMessageCount++;
                }
            }
            /**
             * find out with whom we got a conversation
             */
            $withUsers = array();
            /** @var User $user */
            foreach($conversation->get('users') as $user) {
                if( $user->get('id') != $currentUser->get('id') ) {
                    $withUsers[] = array( 'id' => $user->get('id'), 'username' => $user->get('username') );
                }
            }
            $result[] = array(
                    'id' => $conversation->get('id'),
                    'lastMessage' => $lastMessage,
                    'withUsers' => $withUsers,
                    'unreadMessageCount' => $unreadMessageCount
            );
        }

        die( json_encode($result) );
    }

    /**
     * gets a single conversation by id
     * @param $conversationId
     */
    public function get($conversationId) {
        $conversation = $this->Conversations->findById($conversationId)->first();
        die( json_encode($conversation) );
    }

    /**
     * gets participants of a conversation
     * @param $conversationId
     */
    public function getParticipants($conversationId) {
        $conversation = $this->Conversations->findById($conversationId)->contain( ['Users'] )->first();
        die( json_encode( $conversation->get('users') ) );
    }

    public function addMessage($conversationId) {
        /** @var Conversation $conversation */
        $conversation = $this->Conversations->findById($conversationId)->contain( ['Users'] )->first();

        if( !is_object($conversation) ) {
            throw new NotFoundException(__('Conversation not found! (maybe improper id given)'));
        }

        $result = null;

        $currentUser = $this->Auth->user();

        if( $this->request->is('post') ) {
            $user = $this->Conversations->Users->findById($currentUser['id'])->first();

            $this->createMessage($conversation, $user);
        }

        $this->notifyParticipants($conversation, "newmessages");

        die(json_encode(true));
    }

    /**
     * @param User $receiver
     * @param User $sender
     * @return Conversation a common conversation which is not a group conversation (should be exactly one)
     */
    private function getCommonConversation(User $receiver,User $sender){
        $commonConversation = null;

        foreach($receiver->conversations as $receiverConversation) {
            foreach($sender->conversations as $senderConversation) {
                if( $senderConversation->get('id') == $receiverConversation->get('id') && !$senderConversation->get('isGroup') ) {
                    /**
                     * they already had a conversation which is no group conversation
                     */
                    $commonConversation = $senderConversation;
                }
            }
        }
        return $commonConversation;
    }

    /**
     * @param User $sender
     * @param User $receiver
     * @return Conversation
     */
    private function createNewConversation($sender, $receiver) {
        /**
         * link new conversation to both users
         */
        $newConversation = new Conversation();

        $newConversation->set('isGroup', false);
        $newConversation->users = [
            $sender,
            $receiver
        ];

        $this->Conversations->save($newConversation);

        return $newConversation;
    }

    /**
     * @param Conversation $commonConversation
     * @param User $sender
     */
    private function createMessage($commonConversation, $sender) {
        $message = new Message($this->request->data);
        $message->set('conversation', $commonConversation);
        $message->set('user', $sender);

        $this->Conversations->Messages->save($message);
    }

    /**
     * @param $conversation
     */
    private function notifyParticipants($conversation, $type, $data = []) {
        foreach ($conversation->get('users') as $participant) {
            $entryData = array(
                'category' => '' . $participant->get('id'),
                'type' => $type,
                'data' => $data
            );

            $socket = AppController::getZMQSocket();
            $socket->send(json_encode($entryData));
        }
    }

    public function markAllMessagesRead($conversationId) {
        $conversation = $this->Conversations->findById($conversationId)->contain( ['Messages'] )->first();

        foreach($conversation->messages as $message) {
            $message->set('read', true);
            $message->save();
        }
    }
}