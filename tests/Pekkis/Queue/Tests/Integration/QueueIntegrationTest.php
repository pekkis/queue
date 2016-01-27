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
            array('lussen.meister', array()),
            array('lussen.meister', null),
            array('lussen.meister', 'mordorin tenhunen se mehevää tikkaria lipaisee'),
            array('lussen.meister', $obj),
        );
    }

    /**
     * @dataProvider provideMessages
     * @test
     */
    public function messagesGoThroughThePipeUnchanged($topic, $data)
    {
        $this->assertFalse($this->queue->dequeue());
        $message = $this->queue->enqueue($topic, $data);

        $dequeued = $this->queue->dequeue();

        $this->assertInstanceOf('Pekkis\Queue\Message', $dequeued);

        $this->assertEquals($message->getTopic(), $dequeued->getTopic());
        $this->assertEquals($message->getData(), $dequeued->getData());

        $this->queue->ack($dequeued);

        $this->assertFalse($this->queue->dequeue());
    }
}
