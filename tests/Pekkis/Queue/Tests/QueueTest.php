<?php

namespace Pekkis\Queue\Tests;

use Pekkis\Queue\Queue;
use Pekkis\Queue\Message;

class QueueTest extends \Pekkis\Queue\Tests\TestCase
{

    private $adapter;

    private $queue;

    private $ed;

    public function setUp()
    {
        $this->adapter = $this->getMock('Pekkis\Queue\Adapter\Adapter');
        $this->ed = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->queue = new Queue($this->adapter, $this->ed);
    }

    /**
     * @test
     */
    public function enqueueDelegates()
    {
        $message = Message::create('test-message', array('aybabtu' => 'lussentus'));
        $this->adapter
            ->expects($this->once())
            ->method('enqueue')
            ->with($message)
            ->will($this->returnValue('ret'));
        $this->assertSame('ret', $this->queue->enqueue($message));

    }

    /**
     * @test
     */
    public function dequeueDelegates()
    {
        $message = Message::create('test-message', array('aybabtu' => 'lussentus'));
        $this->adapter->expects($this->once())->method('dequeue')->will($this->returnValue($message));
        $this->assertSame($message, $this->queue->dequeue($message));
    }

    /**
     * @test
     */
    public function ackDelegates()
    {
        $message = Message::create('test-message', array('aybabtu' => 'lussentus'));
        $this->adapter->expects($this->once())->method('ack')->will($this->returnValue('luslus'));
        $this->assertSame('luslus', $this->queue->ack($message));
    }

    /**
     * @test
     */
    public function purgeDelegates()
    {
        $this->adapter->expects($this->once())->method('purge')->will($this->returnValue(true));
        $this->assertTrue($this->queue->purge());
    }

}
