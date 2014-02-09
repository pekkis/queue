<?php

namespace Pekkis\Queue\Example;

require_once (is_file(__DIR__ . '/bootstrap.php')) ? __DIR__ . '/bootstrap.php' : __DIR__ . '/bootstrap.dist.php';

use Pekkis\Queue\Adapter\IronMQAdapter;
use Pekkis\Queue\Data\AbstractDataSerializer;
use Pekkis\Queue\Data\DataSerializer;
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

    public function getFrom()
    {
        return $this->from;
    }

    public function getTo()
    {
        return $this->to;
    }

    public function getMessage()
    {
        return Message::create(
            'reservation.create',
            $this
        );
    }
}

class ReservationRequestDataSerializer extends AbstractDataSerializer implements DataSerializer
{
    public function willSerialize($unserialized)
    {
        return ($unserialized instanceof ReservationRequest);
    }

    public function serialize($reservationRequest)
    {
        return json_encode(
            array(
                'from' => $reservationRequest->getFrom()->format('Y-m-d H:i:s'),
                'to' => $reservationRequest->getTo()->format('Y-m-d H:i:s'),
            )
        );
    }

    public function unserialize($serialized)
    {
        $data = json_decode($serialized, true);
        return new ReservationRequest(
            DateTime::createFromFormat('Y-m-d H:i:s', $data['from']),
            DateTime::createFromFormat('Y-m-d H:i:s', $data['to'])
        );
    }

}

// Creates an IronMQ backed queue
$queue = new Queue(
    new IronMQAdapter(IRONMQ_TOKEN, IRONMQ_PROJECT_ID, 'pekkis-queue-example'),
    new EventDispatcher()
);

$queue->addDataSerializer(new ReservationRequestDataSerializer());

// Create a console output and attach a queue subscriber to get queue event messages to console output.
$output = new ConsoleOutput();
$queue->addSubscriber(new ConsoleOutputSubscriber($output));
