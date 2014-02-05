<?php

namespace Pekkis\Queue\Example;

use Pekkis\Queue\Adapter\IronMQAdapter;
use Pekkis\Queue\Message;
use Pekkis\Queue\Queue;
use Symfony\Component\EventDispatcher\EventDispatcher;

require_once (is_file(__DIR__ . '/bootstrap.php')) ? __DIR__ . '/bootstrap.php' : __DIR__ . '/bootstrap.dist.php';

$queue = new Queue(
    new IronMQAdapter(IRONMQ_TOKEN, IRONMQ_PROJECT_ID, 'pekkis-queue-example'),
    new EventDispatcher()
);

$message = Message::create(
    'pekkis.queue.example',
    array(
        'some' => 'random data'
    )
);

$queue->enqueue($message);
$received = $queue->dequeue();

$data = $received->getData();
var_dump($data);

$queue->ack($received);

