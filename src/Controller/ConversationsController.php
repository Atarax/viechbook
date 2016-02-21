<?php
namespace App\Controller;
use App\Model\Entity\Conversation;
use App\Model\Entity\Message;
use App\Model\Entity\Notification;
use App\Model\Entity\User;
use App\Model\Table\ConversationsTable;
use App\Model\TheViechNotifier;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use ZMQContext;

/**
 * Created by PhpStorm.
 * User: atarax
 * Date: 4/27/14
 * Time: 12:59 AM
 * @property ConversationsTable Conversations
 */
class ConversationsController extends AppController {

<<<<<<< Updated upstream:src/Controller/ConversationsController.php
    /**
     * basically sends a message to a given receiver, taking into account that there might not exists a conversation yet
     * @param $receiverId
     */
    public function addMessageByReceiver($receiverId) {
        $currentUser = $this->Auth->user();
=======
	/**
	 * basically sends a message to a given receiver, taking into account that there might not exists a conversation yet
	 * @param $receiverId
	 * @throws Exception
	 */
    public function add_message_by_receiverAction($receiverId) {
        $auth = $this->session->get('auth');
>>>>>>> Stashed changes:app/MSpace/Controller/ConversationsController.php

        /** @var User $sender */
        $sender = Users::findFirst($auth['id']);

		/** @var User $receiver */
        $receiver = Users::findFirst($receiverId);

        if( is_null($receiver) ) {
            throw new Exception(__('Receiver for message not found! (maybe improper id given)'));
        }
        if( $this->request->isPost() ) {

            /**
             * check if we already have a conversation with the receiver
             */
            $commonConversation = $this->getCommonConversation($receiver, $sender);

            if( is_null($commonConversation) ) {
                $commonConversation = $this->createNewConversation($sender, $receiver);
            }

            $this->createMessage($commonConversation, $sender);

            $this->flash->success(__('The message has been sent!'));
        }

		$this->view->setVar('receiver', $receiver);
    }

    /**
     * return all Conversations for current User as json
     */
    public function listAll() {
        $currentUser = $this->Auth->user();
        $currentUser = $this->Conversations->Users
            ->findById($currentUser['id'])
            ->contain( ['Conversations'] )
            ->contain( ['Notifications'] )
            ->order(['modified' => 'DESC'])
            ->first();

        $conversations = [];

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
            $conversations[] = array(
                'id' => $conversation->get('id'),
                'lastMessage' => $lastMessage,
                'withUsers' => $withUsers
            );
        }

        $response = new \stdClass();
        $response->conversations = $conversations;
        $response->notifications = $currentUser->get('notifications');

        die( json_encode($response) );
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

        $this->notifyParticipants($conversation, Notification::TYPE_NEW_MESSAGE);

        die(json_encode(true));
    }

	/**
	 * @param Users $receiver
	 * @param Users $sender
	 * @return Conversations a common conversation which is not a group conversation (should be exactly one)
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
     * @param $type
     * @param array $data
     */
    private function notifyParticipants($conversation, $type, $data = []) {
        foreach ($conversation->get('users') as $participant) {
            if( $participant->get('id') == $this->Auth->user()['id'] ){
                continue;
            }

            $realTimeNotifier = new TheViechNotifier();
            $realTimeNotifier->notify(
                $participant->get('id'),
                $type,
                $data
            );

            /**
             * notify them persistently
             * but first have a look if the user is already informed
             */
            $notifcationsTable = TableRegistry::get('Notifications');
            $notifications = $notifcationsTable->findAllByTypeAndUser_id(Notification::TYPE_NEW_MESSAGE, $participant->get('id') );

            $participantInformed = false;

            foreach($notifications as $notification) {
               $content = json_decode( $notification->get('content') );
               if( $content->conversation_id == $conversation->get('id') ) {
                   $participantInformed = true;
               }
            }

            if(!$participantInformed) {
                $content = new \stdClass();
                /** @var int conversation_id */
                $content->conversation_id = $conversation->get('id');

                $newNotification = new Notification();
                $newNotification->set('user_id', $participant->get('id') );
                $newNotification->set('content', json_encode($content) );
                $newNotification->set('type', Notification::TYPE_NEW_MESSAGE);
                $this->Conversations->Users->Notifications->save($newNotification);
            }
        }
    }

    public function markAllMessagesRead($conversationId) {
        $conversation = $this->Conversations->findById($conversationId)->contain( ['Messages'] )->first();

        foreach($conversation->messages as $message) {
            $message->set('read', true);
            $message->save();
        }
    }

    public function clearNotifications($conversationId) {
        $notificationsTable = TableRegistry::get('Notifications');
        $currentUserId = $this->Auth->user()['id'];
        $query = $notificationsTable->findAllByTypeAndUser_id(Notification::TYPE_NEW_MESSAGE, $currentUserId)->contain('Users');

        foreach($query as $notification) {
            $content = json_decode($notification->content);

            if($content->conversation_id == $conversationId) {
                $notificationsTable->delete($notification);
                $notifier = new TheViechNotifier();
                $notifier->notify($currentUserId, Notification::TYPE_NOTIFICATION_CHANGED);
                // only got one notification per conversation and user
                break;
            }
        }

        die( json_encode(true) );
    }
}