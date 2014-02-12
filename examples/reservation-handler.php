<?php

namespace Pekkis\Queue\Example;

use Pekkis\Queue\Processor\ConsoleOutputSubscriber;
use Pekkis\Queue\Processor\Processor;
use Pekkis\Queue\SymfonyBridge\EventDispatchingQueue;

require_once __DIR__ . '/real-life-bootstrap.php';

/** @var EventDispatchingQueue $queue */

// Queue processor has it's own console output subscriber to add to our queue.
$queue->addSubscriber(new ConsoleOutputSubscriber($output));
$processor = new Processor($queue);

// Register our handler to handle reservation request messages
$processor->registerHandler(new ReservationHandler());

// A single processor should process only n messages because PHP sometime leaks like a hamster with bladder problems.
$processor->processWhile(function ($ret) {
    static $count = 0;

    $count = $count + 1;
    if ($count >= 1000) {
        return false;
    }

    if (!$ret) {
        sleep(2);
    }
    return true;
});
