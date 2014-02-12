<?php

namespace Pekkis\Queue\Example;

require_once (is_file(__DIR__ . '/bootstrap.php')) ? __DIR__ . '/bootstrap.php' : __DIR__ . '/bootstrap.dist.php';

use Pekkis\Queue\Adapter\IronMQAdapter;
use Pekkis\Queue\Data\AbstractDataSerializer;
use Pekkis\Queue\Data\DataSerializer;
use DateTime;
use Pekkis\Queue\Message;
use Pekkis\Queue\QueueInterface;
use Pekkis\Queue\SymfonyBridge\ConsoleOutputSubscriber;
use Pekkis\Queue\Queue;
use Pekkis\Queue\SymfonyBridge\EventDispatchingQueue;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Pekkis\Queue\Processor\MessageHandler;
use Pekkis\Queue\Processor\Result;

/**
 * Represents some random enqueueable object of relevance of your application.
 * A message in itself is an enqueueable too so you don't need to implement this if you don't need / want to.
 */
class ReservationRequest
{
    /**
     * @var \DateTime
     */
    private $from;

    /**
     * @var \DateTime
     */
    private $to;

    public function __construct(DateTime $from, DateTime $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getTo()
    {
        return $this->to;
    }
}

/**
 * Serializes our reservation requests
 */
class ReservationRequestDataSerializer extends AbstractDataSerializer implements DataSerializer
{
    public function willSerialize($unserialized)
    {
        return ($unserialized instanceof ReservationRequest);
    }

    public function serialize($reservationRequest)
    {
        return serialize($reservationRequest);
    }

    public function unserialize($serialized)
    {
        return unserialize($serialized);
    }
}

/**
 * A message handler to handle messages dequeued from the queue.
 *
 * You can run 1-n handlers at the same time, of course.
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
    public function handle(Message $message, QueueInterface $queue)
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

// Creates an IronMQ backed queue
$innerQueue = new Queue(
    new IronMQAdapter(IRONMQ_TOKEN, IRONMQ_PROJECT_ID, 'pekkis-queue-example')
);
// Adds our own data serializer for reservation requests
$innerQueue->addDataSerializer(new ReservationRequestDataSerializer());

// Wrap the queue with Symfony events
$queue = new EventDispatchingQueue(
    $innerQueue,
    new EventDispatcher()
);

// Create a console output and attach a queue subscriber to get queue event messages to console output.
$output = new ConsoleOutput();
$queue->addSubscriber(new ConsoleOutputSubscriber($output));
