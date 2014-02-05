<?php

namespace Pekkis\Queue\Example;

require_once __DIR__ . '/bootstrap.php';

use Pekkis\Queue\Adapter\IronMQAdapter;
use Pekkis\Queue\Enqueueable;
use DateTime;
use Pekkis\Queue\Message;
use Pekkis\Queue\ConsoleOutputSubscriber;
use Pekkis\Queue\Queue;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Represents some random enqueueable object of relevance of your application.
 * A message in itself is an enqueueable too so you don't need to implement this if you don't need / want to.
 */
class ReservationRequest implements Enqueueable
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

    public function getMessage()
    {
        return Message::create(
            'reservation.create',
            array(
                'from' => $this->from->format('Y-m-d H:i:s'),
                'to' => $this->to->format('Y-m-d H:i:s'),
            )
        );
    }
}

// Creates an IronMQ backed queue
$queue = new Queue(
    new IronMQAdapter(IRONMQ_TOKEN, IRONMQ_PROJECT_ID, 'pekkis-queue-example'),
    new EventDispatcher()
);

// Create a console output and attach a queue subscriber to get queue event messages to console output.
$output = new ConsoleOutput();
$queue->addSubscriber(new ConsoleOutputSubscriber($output));
