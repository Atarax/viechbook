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

global $currentRequestHash;

$connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
$channel = $connection->channel();
$workerQueueName = 'fetch_stats';
$channel->queue_declare($workerQueueName, false, false, false, false);
$processId = posix_getpid();

$work = function($msg) use($channel, $processId) {
	global $currentRequestHash;

	$message = json_decode($msg->body);

	echo 'Serving request ', $message->requestHash, "\n";
	//sleep(1);
	usleep(50000);
	$currentRequestHash = $message->requestHash;

	echo 'Finished! Notifying Host.', "\n";;

	$workerFinishedQueueName = $currentRequestHash . '_finished_workers';
	$messageContent = [
		'job' => $message->jobNumber,
		'process' => $processId
	];

	$msg = new AMQPMessage( json_encode($messageContent) );
	$channel->basic_publish($msg, '', $workerFinishedQueueName);

	echo "Host Notified.\n";
};

while(true) {
	$channel->basic_consume($workerQueueName, '', false, true, false, false, $work);

	echo 'Waiting for work', "\n";;

	while(count($channel->callbacks)) {
		$channel->wait();
	}
}

$channel->close();
$connection->close();