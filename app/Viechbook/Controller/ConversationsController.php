<?php
namespace Viechbook\Controller;

use Cake\Network\Exception\NotFoundException;
use Exception;
use Viechbook\Library\TheViechNotifier;
use Viechbook\Model\Conversations;
use Viechbook\Model\ConversationsUsers;
use Viechbook\Model\Messages;
use Viechbook\Model\ModelBase;
use Viechbook\Model\Notifications;
use Viechbook\Model\Users;
use ZMQContext;

/**
 * Created by PhpStorm.
 * User: atarax
 * Date: 4/27/14
 * Time: 12:59 AM
 * @property ConversationsTable Conversations
 */
class ConversationsController extends ControllerBase {

	/**
	 * basically sends a message to a given receiver, taking into account that there might not exists a conversation yet
	 * @param $receiverId
	 * @return int
	 * @throws Exception
	 */
    public function add_message_by_receiverAction($receiverId) {
        $auth = $this->session->get('auth');

        /** @var Users $sender	*/
        $sender = Users::findFirst($auth['id']);
		/** @var Users $receiver */
        $receiver = Users::findFirst($receiverId);

        if( is_null($receiver) ) {
            throw new Exception( 'Receiver for message not found! (maybe improper id given)' );
        }

        if( $this->request->isPost() ) {
            /** check if we already have a conversation with the receiver */
            $commonConversation = $this->getCommonConversation($receiver, $sender);

            if( is_null($commonConversation) ) {
				$commonConversation = $this->createConversation($sender, $receiver);
            }

            $this->createMessage($commonConversation->getId(), $sender->getId());
			$this->notifyParticipants($commonConversation, Notifications::TYPE_NEW_MESSAGE);

            $this->flash->success( 'The message has been sent!' );

			return $this->dispatcher->forward(array(
				'controller' => 'users',
				'action' => 'messages',
				'parameters' => [$commonConversation->getId()]
			));

		}

		$this->view->setVar('receiver', $receiver);
    }

    /**
     * return all Conversations for current User as json
     */
    public function list_allAction() {
		$auth = $this->session->get('auth');

		/** @var Users $currentUser */
		$currentUser = Users::findFirst($auth['id']);

        $conversationsResult = [];

        foreach($currentUser->getConversations(['order' => 'Viechbook\\Model\\Conversations.modified desc']) as $conversation) {

			// TODO make more efficient by using query parameters $messages
			$messages = $conversation->getUserMessages();

            /** @var Messages $lastMessage
             * get the message with the biggest id, resulting hopefully in newest message
             */
            $messageCount = count($messages);
            $lastMessage = $conversation->getUserMessages()[ $messageCount - 1 ];

            /** find out with whom we got a conversation */
            $withUsers = array();

            /** @var Users $user */
            foreach($conversation->getUsers() as $user) {
				if( $user->getId() != $auth['id'] ) {
                    $withUsers[] = array( 'id' => $user->getId(), 'username' => $user->getUsername() );
                }
            }

            $conversationsResult[] = array(
                'id' => $conversation->getId(),
                'lastMessage' => $lastMessage,
                'withUsers' => $withUsers
            );
        }

		$response = new \stdClass();
        $response->conversations = $conversationsResult;
        $response->notifications = $currentUser->getNotifications()->toArray();

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
    public function get_participantsAction($conversationId) {
		/** @var Conversations $conversation */
        $conversation = Conversations::findFirst($conversationId);
        die( json_encode( $conversation->getUsers()->toArray() ) );
    }

    public function add_messageAction($conversationId) {
        /** @var Conversations $conversation */
        $conversation = Conversations::findFirst($conversationId);

        if( !is_object($conversation) ) {
            throw new NotFoundException(__('Conversation not found! (maybe improper id given)'));
        }

        $result = null;

        $currentUser = $this->session->get('auth');

			if( $this->request->isPost() ) {
           		$this->createMessage($conversation->getId(), $currentUser['id']);
        	}

        $this->notifyParticipants($conversation, Notifications::TYPE_NEW_MESSAGE, ['conversation_id' => $conversationId]);

        die(json_encode(true));
    }

	/**
	 * @param Users $receiver
	 * @param Users $sender
	 * @return Conversations a common conversation which is not a group conversation (should be exactly one)
	 */
    private function getCommonConversation(Users $receiver,Users $sender){
        $commonConversation = null;

		$receiverConversations = ConversationsUsers::find([
			'user_id = :user_id: ',
			'bind' => ['user_id' => $receiver->getId()]
		]);

		$senderConversations = ConversationsUsers::find([
			'user_id = :user_id: ',
			'bind' => ['user_id' => $sender->getId()]
		]);

		foreach($receiverConversations as $receiverConversation) {

            foreach($senderConversations as $senderConversation) {

                if( $senderConversation->getConversation_id() == $receiverConversation->getConversation_id() ) {
					/** find it and check if its a group-conversation */
					$possibleConversation = Conversations::findFirst( $senderConversation->getConversation_id() );

					if( !$possibleConversation->getIsGroup() ) {
						/** found one! */
						$commonConversation = $possibleConversation;
						break;
					}
                }
            }

			/** stop searching */
			if( !is_null($commonConversation) ) {
				break;
			}
        }

        return $commonConversation;
    }

    /**
     * @param Users $sender
     * @param Users $receiver
     * @return Conversations
     */
    private function createConversation($sender, $receiver) {
        /** create new conversation */
		$newConversation = new Conversations();

        $newConversation->setIsGroup(0);
		$newConversation->save();

		/** link new conversation to both users */
		foreach( [$sender, $receiver] as $user ) {
			$link = new ConversationsUsers();
			$link->setUser_id( $user->getId() );
			$link->setConversation_id( $newConversation->getId() );
			$link->save();
		}

		return $newConversation;
    }

    /**
     * @param int $commonConversationId
     * @param int $senderId
     */
    private function createMessage($commonConversationId, $senderId) {
        $message = new Messages();

		$message->setConversationId($commonConversationId);
		$message->setContent( $this->request->getPost('content') );
        $message->setUserId($senderId);
		$message->setRead(0);

        $message->save();

		/** after also set the conversation modified, makes ordering simpler */
		$conversation = Conversations::findFirst($commonConversationId);
		$conversation->save();
	}

    /**
     * @param Conversations $conversation
     * @param $type
     * @param array $data
     */
    private function notifyParticipants($conversation, $type, $data = []) {
		$authId = $this->session->get('auth')['id'];

        foreach ($conversation->getUsers() as $participant) {

			if( $participant->getId() == $authId){
                continue;
            }

            $realTimeNotifier = new TheViechNotifier();
            $realTimeNotifier->notify(
                $participant->getId(),
                $type,
                $data
            );

            /**
             * notify them persistently
             * but first have a look if the user is already informed
			 *
			 * @var Notifications[] $notification
             */
			$notifications = Notifications::find([
				'type' => Notifications::TYPE_NEW_MESSAGE,
				'user_id' => $participant->getId()
			]);

            $participantInformed = false;
			$conversationId = $conversation->getId();

			/** @var Notifications $notification */
			foreach($notifications as $notification) {
               $content = json_decode( $notification->getContent() );
				if( $content->conversation_id == $conversationId) {
                   $participantInformed = true;
               }
            }

            if(!$participantInformed) {
                $content = new \stdClass();
                /** @var int conversation_id */
                $content->conversation_id = $conversationId;

                $newNotification = new Notifications();
                $newNotification->setUserId( $participant->getId() );
                $newNotification->setContent( json_encode($content) );
                $newNotification->setType(Notifications::TYPE_NEW_MESSAGE);

				$newNotification->save();
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

    public function clear_notificationsAction($conversationId) {
        $notifications = Notifications::find([
			'conversations_id' => $conversationId
		]);

		$auth = $this->session->get('auth');

        foreach($notifications as $notification) {
            $content = json_decode($notification->content);

            if($content->conversation_id == $conversationId) {
                $notification->delete();
                $notifier = new TheViechNotifier();
                $notifier->notify($auth['id'], Notifications::TYPE_NOTIFICATION_CHANGED);
                // only got one notification per conversation and user
                break;
            }
        }

        die( json_encode(true) );
    }
}