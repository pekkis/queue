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

Version upgrades
-----------------

Refer to UPGRADE.md

Todo
-----

SymfonyBridge and Processor sub-packages will be separated to exist in their own packages.
