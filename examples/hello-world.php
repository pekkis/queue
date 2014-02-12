<?php

namespace Pekkis\Queue\Example;

use Pekkis\Queue\Adapter\IronMQAdapter;
use Pekkis\Queue\Message;
use Pekkis\Queue\Queue;

require_once (is_file(__DIR__ . '/bootstrap.php')) ? __DIR__ . '/bootstrap.php' : __DIR__ . '/bootstrap.dist.php';

// Create a new IronMQ backed queue
$queue = new Queue(
    new IronMQAdapter(IRONMQ_TOKEN, IRONMQ_PROJECT_ID, 'pekkis-queue-example')
);

// Queues can be emptied.
$queue->purge();

// A message consists of a topic and data. A message instance with an UUID you can use is returned.
$message = $queue->enqueue(
    'pekkis.queue.example',
    array(
        'some' => 'random data'
    )
);

// Dequeue and process a single message
$received = $queue->dequeue();
$data = $received->getData();
var_dump($data);

// Acknowledge the message (you're done with it)
$queue->ack($received);
