<?php

namespace Pekkis\Queue\Example;

use Pekkis\Queue\Message;
use Pekkis\Queue\Processor\ConsoleOutputSubscriber;
use Pekkis\Queue\Processor\MessageHandler;
use Pekkis\Queue\Processor\Processor;
use Pekkis\Queue\Processor\Result;
use Pekkis\Queue\Queue;

require_once __DIR__ . '/real-life-bootstrap.php';

/**
 * A message handler to handle 1-n types of messages dequeued from the queue
 */
class ReservationHandler implements MessageHandler
{
    /**
     * @param Message $message
     * @return bool
     */
    public function willHandle(Message $message)
    {
        return ($message->getType() == 'reservation.create');
    }

    /**
     * @param Message $message
     * @return Result
     */
    public function handle(Message $message, Queue $queue)
    {
        /** @var ReservationRequest $reservation */
        $reservation = $message->getData();

        if (rand(1, 100) >= 75) {
            // If a result is not successful the message will stay on the queue.
            $result = new Result(false, 'Oh dear, the reservation could not be created. It will be retried... soon!');
        } else {

            $msg = sprintf(
                "Reservation created from %s to %s",
                $reservation->getFrom()->format('Y-m-d H:i:d'),
                $reservation->getTo()->format('Y-m-d H:i:d')
            );

            // If a result is successful, the message is acked (acknowledged to be processed, removed from queue)
            $result = new Result(true, $msg);
        }

        return $result;
    }
}

/** @var Queue $queue */
$queue->addSubscriber(new ConsoleOutputSubscriber($output));
$processor = new Processor($queue);

// Register our handler to handle reservation request messages
$processor->registerHandler(new ReservationHandler());

// Process messages until the queue is empty
do {
    // True means a message was processed from the queue. False means empty queue.
    $ret = $processor->process();

} while ($ret);


