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
        $this->adapter->purge();

        $this->adapter->enqueue($this->message);

        list ($message, $identifier, $internals) = $this->adapter->dequeue();
        $this->assertEquals($this->message, $message);

        $this->adapter->ack($identifier, $internals);
        $this->assertFalse($this->adapter->dequeue());
    }

    /**
     * @test
     */
    public function dequeueShouldReturnFalseIfQueueIsEmpty()
    {
        $this->adapter->purge();
        $message = $this->adapter->dequeue();
        $this->assertFalse($message);
    }

    /**
     * @test
     */
    public function purgeShouldResultInAnEmptyQueue()
    {
        $this->adapter->purge();

        for ($x = 10; $x <= 10; $x++) {
            $this->adapter->enqueue("message {$x}");
        }

        list ($msg, $identifier, $internals) = $this->adapter->dequeue();
        $this->assertInternalType('string', $msg);

        $this->adapter->ack($identifier, $internals);

        $this->adapter->purge();

        $this->assertFalse($this->adapter->dequeue());
    }

    /**
     * @test
     * @runInSeparateProcess
     */
    public function queueShouldResendMessageOnlyIfMessageIsNotAcked()
    {
        $queue = $this->getAdapter();
        $queue->purge();

        $this->assertFalse($queue->dequeue());

        $message = 'messago mucho masculino';
        $queue->enqueue($message);

        list ($dequeued, $identifier, $internals) = $queue->dequeue();

        $this->assertEquals($message, $dequeued);
        $this->assertFalse($queue->dequeue());

        return $message;
    }

    /**
     * @test
     * @param $message
     * @depends queueShouldResendMessageOnlyIfMessageIsNotAcked
     * @runInSeparateProcess
     *
     */
    public function queueShouldResendMessageOnlyIfMessageIsNotAcked2($message)
    {
        if ($sleepyTime = $this->getSleepyTime()) {
            sleep($sleepyTime);
        }

        $queue = $this->getAdapter();

        list ($dequeued, $identifier, $internals) = $queue->dequeue();
        $this->assertEquals($message, $dequeued);

        $queue->ack($identifier, $internals);
    }

    /**
     * @test
     * @param $message
     * @depends queueShouldResendMessageOnlyIfMessageIsNotAcked2
     * @runInSeparateProcess
     *
     */
    public function queueShouldResendMessageOnlyIfMessageIsNotAcked3($message)
    {
        if ($sleepyTime = $this->getSleepyTime()) {
            sleep($sleepyTime);
        }

        $queue = $this->getAdapter();
        $this->assertFalse($queue->dequeue());
    }
}
