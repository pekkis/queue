<?php

namespace Pekkis\Queue\Tests\Adapter;

use Pekkis\Queue\Adapter\Adapter;
use Pekkis\Queue\Data\ArrayDataSerializer;
use Pekkis\Queue\Data\SerializedData;
use Pekkis\Queue\Message;

abstract class TestCase extends \Pekkis\Queue\Tests\TestCase
{
    /**
     *
     * @var Adapter
     */
    protected $adapter;

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var ArrayDataSerializer
     */
    protected $serializer;

    public function setUp()
    {
        $this->serializer = new ArrayDataSerializer();

        $this->message = $this->createMessage('test-message', array('aybabtu' => 'lussentus'));


        $this->adapter = $this->getAdapter();
        $this->adapter->purge();
    }

    abstract protected function getAdapter();

    protected function getSleepyTime()
    {
        return 1;
    }

    protected function createMessage($type, array $data)
    {
        $message = Message::create(
            'test-message',
            new SerializedData($this->serializer->getIdentifier(), $this->serializer->serialize($data))
        );
        return $message;
    }


    /**
     * @test
     * @return Queue
     */
    public function dequeueShouldDequeueEnqueuedMessage()
    {
        $this->adapter->enqueue($this->message);

        $message = $this->adapter->dequeue();
        $this->assertInstanceOf('Pekkis\Queue\Message', $message);
        $this->adapter->ack($message);

        $this->assertInternalType('string', $message->getData());
        $this->assertEquals($this->message->getUuid(), $message->getUuid());
        $this->assertEquals($this->message->getType(), $message->getType());

        $this->assertNotNull($message->getIdentifier());
    }

    /**
     * @test
     */
    public function dequeueShouldReturnNullIfQueueIsEmpty()
    {
        $message = $this->adapter->dequeue();
        $this->assertNull($message);
    }

    /**
     * @test
     */
    public function purgeShouldResultInAnEmptyQueue()
    {
        for ($x = 10; $x <= 10; $x++) {
            $this->adapter->enqueue($this->createMessage('testosteron', array('count' => $x)));
        }

        $msg = $this->adapter->dequeue();
        $this->assertNotNull($msg);
        $this->adapter->ack($msg);

        $this->adapter->purge();

        $this->assertNull($this->adapter->dequeue());

    }

   /**
     * @test
     */
    public function queueShouldResendIfMessageIsNotAcked()
    {
        $queue = $this->getAdapter();
        $queue->purge();

        $this->assertNull($queue->dequeue());

        $message = $this->createMessage('testosteron', array('mucho' => 'masculino'));
        $queue->enqueue($message);

        $this->assertInstanceOf('Pekkis\Queue\Message', $queue->dequeue());
        $this->assertNull($queue->dequeue());

        unset($queue);
        gc_collect_cycles();

        if ($sleepyTime = $this->getSleepyTime()) {
            sleep($sleepyTime);
        }

        $queue = $this->getAdapter();

        $msg = $queue->dequeue();
        $this->assertInstanceOf('Pekkis\Queue\Message', $msg);

        $queue->ack($msg);

    }

}
