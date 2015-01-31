<?php
namespace Viechbook\Controller;
use Phalcon\Mvc\View;
use Viechbook\Model\Messages;
use Viechbook\Model\Users;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 4/27/14
 * Time: 12:59 AM
 */
class MessagesController extends ControllerBase {

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

    public function get_by_conversationAction($conversationId = null) {
		/** @var Messages[] $messages */
		$messages = Messages::find( ['conversation_id=' . $conversationId ] );

		$users = [];
		$result = [];

		foreach($messages as $index => $message) {
			/** @var Messages $message */
			$userId = $message->getUserId();

			/** @var Users  $user */
			if( !isset( $users[$userId] ) ) {
				$user = Users::findFirst($userId);
			}
			else {
				$user = $users[$userId];
			}

			$row = $message->toArray();

			$row['user'] = $user->toArray();

			$result[] = $row;
		}

        echo json_encode($result);

		// Only output json
		$this->view->setRenderLevel(View::LEVEL_NO_RENDER);
    }
}