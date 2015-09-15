<?php
namespace Viechbook\Controller;
use Phalcon\Exception;
use Phalcon\Mvc\View;
use Phalcon\Paginator\Adapter\QueryBuilder;
use Viechbook\Model\ConversationsUsers;
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

    public function get_by_conversationAction($conversationId, $currentPageNumber = 0) {
		/** check for permissions to see the messages */
		$link = ConversationsUsers::findFirst(['conversation_id' => $conversationId, 'user_id' => $this->currentUser->id]);

		if( !is_object($link) ){
			throw new Exception('Not allowed to acces that conversation!');
		}

		$builder = $this->modelsManager->createBuilder()
			->from('Viechbook\Model\Messages')
			->where('conversation_id = ' . intval($conversationId))
			->orderBy('id DESC');


		$paginator = new QueryBuilder(
			array(
				"builder"  => $builder,
				"limit" => 40,
				"page"  => $currentPageNumber
			)
		);

		$users = [];
		$result = [];

		$page = $paginator->getPaginate();

		foreach($page->items as $index => $message) {
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

		/** order asc is fine for pagination but we need the messages in chronological order */
		$result = array_reverse($result);
        echo json_encode($result);

		// Only output json
		$this->view->setRenderLevel(View::LEVEL_NO_RENDER);
    }
}