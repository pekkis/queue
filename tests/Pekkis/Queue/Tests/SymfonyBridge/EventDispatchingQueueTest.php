<?php

namespace Pekkis\Queue\Tests\SymfonyBridge;

use Pekkis\Queue\Message;
use Pekkis\Queue\SymfonyBridge\EventDispatchingQueue;
use Pekkis\Queue\Tests\TestCase;
use Pekkis\Queue\SymfonyBridge\Events;

class EventDispatchingQueueTest extends TestCase
{
    private $innerqueue;

    /**
     * @var EventDispatchingQueue
     */
    private $queue;

    private $ed;

    public function setUp()
    {
        $this->innerqueue = $this->createMock('Pekkis\Queue\QueueInterface');
        $this->ed = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->queue = new EventDispatchingQueue($this->innerqueue, $this->ed);
    }

    /**
     * @test
     */
    public function dequeues()
    {
        $message = Message::create('lus');

        $this->innerqueue
            ->expects($this->once())
            ->method('dequeue')
            ->will($this->returnValue($message));

        $this->ed
            ->expects($this->once())
            ->method('dispatch')
            ->with(Events::DEQUEUE, $this->isInstanceOf('Pekkis\Queue\SymfonyBridge\MessageEvent'));

        $ret = $this->queue->dequeue();
        $this->assertSame($message, $ret);
    }

    /**
     * @test
     */
    public function enqueues()
    {
        $this->ed
            ->expects($this->once())
            ->method('dispatch')
            ->with(Events::ENQUEUE, $this->isInstanceOf('Pekkis\Queue\SymfonyBridge\MessageEvent'));

        $message = Message::create('lus');

        $this->innerqueue
            ->expects($this->once())
            ->method('enqueue')
            ->with('lussogrande', array('tenhunen' => 'imaisee'))
            ->will($this->returnValue($message));

        $ret = $this->queue->enqueue('lussogrande', array('tenhunen' => 'imaisee'));
        $this->assertSame($message, $ret);

    }

    /**
     * @test
     */
    public function acks()
    {
        $message = Message::create('lus');

        $this->ed
            ->expects($this->once())
            ->method('dispatch')
            ->with(Events::ACK, $this->isInstanceOf('Pekkis\Queue\SymfonyBridge\MessageEvent'));

        $this->innerqueue
            ->expects($this->once())
            ->method('ack')
            ->with($message)
            ->will($this->returnValue(true));

        $ret = $this->queue->ack($message);
        $this->assertTrue($ret);
    }

    /**
     * @test
     */
    public function purges()
    {
        $this->ed
            ->expects($this->once())
            ->method('dispatch')
            ->with(Events::PURGE);

        $this->innerqueue
            ->expects($this->once())
            ->method('purge')
            ->will($this->returnValue(true));

        $ret = $this->queue->purge();
        $this->assertTrue($ret);
    }

    /**
     * @test
     */
    public function getterReturnsEventDispatcher()
    {
        $this->assertSame($this->ed, $this->queue->getEventDispatcher());
    }

    /**
     * @test
     */
    public function addSubscriberDelegatesToEventDispatcher()
    {
        $subscriber = $this->createMock('Symfony\Component\EventDispatcher\EventSubscriberInterface');
        $this->ed->expects($this->once())->method('addSubscriber')->with($subscriber);

        $ret = $this->queue->addSubscriber($subscriber);
        $this->assertSame($this->queue, $ret);

    }
}
