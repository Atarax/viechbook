<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 22.11.14
 * Time: 19:38
 */
namespace MSpace\Shell;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


require 'vendor/autoload.php';

$workerPoolQueueName = 'fetch_stats';
$requestHash = substr( md5( time() ), 0, 5);
$workerFinishedQueueName = $requestHash . '_finished_workers';
$workerCount = 5000;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$channel->queue_declare($workerPoolQueueName, false, false, false, false);
$channel->queue_declare($workerFinishedQueueName, false, false, false, false);

echo 'Publishing work to workers.', "\n";

$messageContent = $requestHash;

for($i = 0; $i < $workerCount; $i++) {
	$msg = new AMQPMessage($messageContent);
	$channel->basic_publish($msg, '', $workerPoolQueueName);
}

$gatherWorkers = function($msg) use($workerCount, $channel) {
	global $workersDone;

	$workersDone++;
	echo $workersDone . ' workers done', "\n";

	if( $workersDone === $workerCount ) {
		//exit(1);
		$channel->callbacks = [];
	}
};

$channel->basic_consume($workerFinishedQueueName, '', false, true, false, false, $gatherWorkers);

echo 'Waiting for workers to finish. Queue name:', $workerFinishedQueueName, "\n";

while(count($channel->callbacks)) {
	$channel->wait();
}

echo 'Finished', "\n";;

$channel->queue_delete($workerFinishedQueueName);

$channel->close();
$connection->close();