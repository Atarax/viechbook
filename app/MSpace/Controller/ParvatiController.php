<?php

namespace MSpace\Controller;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Created by PhpStorm.
 * User: cite
 * Date: 16.12.14
 * Time: 00:24
 */

class ParvatiController extends ControllerBase {
	private $randomNumberIndex;

	public function indexAction() {
		$this->view->setVar('data', 'hello');
	}

	public function rabbitAction() {
		$workerPoolQueueName = 'fetch_stats';
		$requestHash = substr( md5( time() ), 0, 5);
		$workerFinishedQueueName = $requestHash . '_finished_workers';
		$workerCount = 100;

		$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
		$channel = $connection->channel();
		$channel->queue_declare($workerPoolQueueName, false, false, false, false);
		$channel->queue_declare($workerFinishedQueueName, false, false, false, false);

		echo 'Publishing work to workers.', "<br>\n";

		$messageContent = [
			'requestHash' => $requestHash,
			'jobNumber' => 0
		];

		for($i = 0; $i < $workerCount; $i++) {
			$messageContent['jobNumber'] = $i;

			$msg = new AMQPMessage( json_encode($messageContent) );
			$channel->basic_publish($msg, '', $workerPoolQueueName);
		}

		global $randomNumber;
		$randomNumber = 1;

		$gatherWorkers = function($msg) use($workerCount, $channel) {
			global $workersDone;
			global $randomNumber;

			$msg  = json_decode($msg->body);

			$workersDone++;
			//echo $msg->process, " - (", $msg->job, ")<br>\n";

			$randomNumber = $this->generateRandomNumber($randomNumber, $msg->job);

			if( $workersDone === $workerCount ) {
				//exit(1);
				$channel->callbacks = [];
			}
		};

		$channel->basic_consume($workerFinishedQueueName, '', false, true, false, false, $gatherWorkers);

		echo 'Waiting for workers to finish. Queue name:', $workerFinishedQueueName, "<br>\n";

		while(count($channel->callbacks)) {
			$channel->wait();
		}

		echo 'Finished ', $randomNumber,  " \n";;

		$channel->queue_delete($workerFinishedQueueName);

		$channel->close();
		$connection->close();

		ob_flush();
		ob_end_flush();
		die();
	}

	private function generateRandomNumber($current, $jobNumber) {
		$jobNumber++;

		if($this->randomNumberIndex % 2 == 0) {
			$current += $jobNumber;
		}
		else {
			$current -= $jobNumber;
		}

		$this->randomNumberIndex++;

		return $current;
	}
}
