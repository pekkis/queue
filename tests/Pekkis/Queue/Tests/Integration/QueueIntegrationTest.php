<?php

namespace Pekkis\Queue\Tests\Integration;

use Pekkis\Queue\Adapter\PhpAMQPAdapter;
use Pekkis\Queue\Enqueueable;
use Pekkis\Queue\Message;
use Pekkis\Queue\Queue;
use Pekkis\Queue\Tests\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class QueueIntegrationTest extends TestCase
{
    /**
     * @var Queue
     */
    private $queue;

    public function setUp()
    {
        $this->queue = new Queue(
            new PhpAMQPAdapter(
                RABBITMQ_HOST,
                RABBITMQ_PORT,
                RABBITMQ_USERNAME,
                RABBITMQ_PASSWORD,
                RABBITMQ_VHOST,
                'test_exchange',
                'test_queue'
            ),
            new EventDispatcher()
        );
        $this->queue->purge();
    }

    public function provideMessages()
    {
        $obj = new \stdClass();
        $obj->lusser = 'nönnönnöö';
        $obj->nuller = null;

        return array(
            array(Message::create('lussen.meister', array())),
            array(Message::create('lussen.meister', null)),
            array(Message::create('lussen.meister', 'mordorin tenhunen se mehevää tikkaria lipaisee')),
            array(Message::create('lussen.meister', $obj)),
        );
    }

    /**
     * @dataProvider provideMessages
     * @test
     */
    public function messagesGoThroughThePipeUnchanged(Enqueueable $enqueueable)
    {
        $message = $enqueueable->getMessage();

        $this->assertFalse($this->queue->dequeue());
        $this->queue->enqueue($enqueueable);

        $dequeued = $this->queue->dequeue();

        $this->assertInstanceOf('Pekkis\Queue\Message', $dequeued);

        $this->assertEquals($message->getType(), $dequeued->getType());
        $this->assertEquals($message->getData(), $dequeued->getData());

        $this->queue->ack($dequeued);

        $this->assertFalse($this->queue->dequeue());

        return $enqueueable;
    }
}
