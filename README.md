Pekkis Queue
=============

[![Build Status](https://secure.travis-ci.org/pekkis/queue.png?branch=master)](http://travis-ci.org/pekkis/queue)

A small, opinionated queue abstraction library based on discovered needs.
Extracted from Xi Filelib and other assorted projects.

What it does?
--------------

Everything implementing the interface Enqueueable can be queued. A queue processor listens to a queue. Back comes a Message.
MessageHandlers handle messages. They return a result with a success flag. New enqueueables may be queued from
a result.

Quickstart
-----------

```php
<?php

namespace Pekkis\Queue\Example;

use Pekkis\Queue\Adapter\IronMQAdapter;
use Pekkis\Queue\Message;
use Pekkis\Queue\Queue;
use Symfony\Component\EventDispatcher\EventDispatcher;

const IRONMQ_TOKEN = 'your-ironmq-token';
const IRONMQ_PROJECT_ID = 'your-ironmq-project-id';

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

```

A better example
-----------------

For an end-to-end example with IronMQ and a "real life" scenario, see the folder `examples`.

Also see Xi Filelib (v0.10+) for actual real use case from the real world!

Ideas / wishes? Contact or create a pull request! Cheers!

Supported queues
-----------------

- RabbitMQ (via PECL and pure PHP).
- IronMQ
- Amazon SQS
