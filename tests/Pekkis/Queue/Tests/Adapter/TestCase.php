<?php

namespace Pekkis\Queue\Tests\Adapter;

use Pekkis\Queue\Adapter\Adapter;
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

    public function setUp()
    {
        $this->adapter = $this->getAdapter();
        $this->adapter->purge();

        $this->message = 'test-message';
    }

    /**
     * @return Adapter
     */
    abstract protected function getAdapter();

    protected function getSleepyTime()
    {
        return 1;
    }

    protected function createMessage($type, array $data)
    {
    }


    /**
     * @test
     */
    public function dequeueShouldDequeueEnqueuedMessage()
    {
        $this->adapter->enqueue($this->message);

        list ($message, $identifier) = $this->adapter->dequeue();
        $this->assertEquals($this->message, $message);

        $this->adapter->ack($identifier);

        $this->assertFalse($this->adapter->dequeue());
    }

    /**
     * @test
     */
    public function dequeueShouldReturnFalseIfQueueIsEmpty()
    {
        $message = $this->adapter->dequeue();
        $this->assertFalse($message);
    }

    /**
     * @test
     */
    public function purgeShouldResultInAnEmptyQueue()
    {
        for ($x = 10; $x <= 10; $x++) {
            $this->adapter->enqueue("message {$x}");
        }

        list ($msg, $identifier) = $this->adapter->dequeue();
        $this->assertInternalType('string', $msg);
        $this->adapter->ack($identifier);

        $this->adapter->purge();

        $this->assertFalse($this->adapter->dequeue());
    }

    /**
     * @test
     */
    public function queueShouldResendMessageOnlyIfMessageIsNotAcked()
    {
        $queue = $this->getAdapter();
        $queue->purge();

        $this->assertFalse($queue->dequeue());

        $message = 'messago mucho masculino';
        $queue->enqueue($message);

        list ($dequeued, $identifier) = $queue->dequeue();

        $this->assertEquals($message, $dequeued);
        $this->assertFalse($queue->dequeue());

        unset($queue);
        gc_collect_cycles();

        if ($sleepyTime = $this->getSleepyTime()) {
            sleep($sleepyTime);
        }

        $queue = $this->getAdapter();

        list ($dequeued, $identifier) = $queue->dequeue();
        $this->assertEquals($message, $dequeued);

        $queue->ack($identifier);

        unset($queue);
        gc_collect_cycles();

        if ($sleepyTime = $this->getSleepyTime()) {
            sleep($sleepyTime);
        }

        $queue = $this->getAdapter();
        $this->assertFalse($queue->dequeue());
    }
}
