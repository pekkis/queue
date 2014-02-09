<?php

namespace Pekkis\Queue\Tests;

use Pekkis\Queue\Data\ArrayDataSerializer;
use Pekkis\Queue\Data\SerializedData;
use Pekkis\Queue\Events;
use Pekkis\Queue\Queue;
use Pekkis\Queue\Message;

class QueueTest extends \Pekkis\Queue\Tests\TestCase
{

    private $adapter;

    /**
     * @var Queue
     */
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
            ->with($this->isType('string'))
            ->will(
                $this->returnCallback(
                    function ($str) {
                        return $str;
                    }
                )
            );

        $this->ed
            ->expects($this->once())
            ->method('dispatch')
            ->with(Events::ENQUEUE, $this->isInstanceOf('Pekkis\Queue\MessageEvent'));

        $output = $this->queue->enqueue($message);

        return $output;
    }

    /**
     * @test
     * @depends enqueueDelegates
     */
    public function dequeueDelegates($input)
    {
        $this->ed
            ->expects($this->once())
            ->method('dispatch')
            ->with(Events::DEQUEUE, $this->isInstanceOf('Pekkis\Queue\MessageEvent'));

        $this->adapter->
            expects($this->once())
            ->method('dequeue')
            ->will($this->returnValue(array($input, 'aybabtu')));

        $dequeued = $this->queue->dequeue();
        $this->assertInstanceof('Pekkis\Queue\Message', $dequeued);

        $this->assertEquals('aybabtu', $dequeued->getIdentifier());
        $this->assertEquals('test-message', $dequeued->getType());
        $this->assertEquals(array('aybabtu' => 'lussentus'), $dequeued->getData());
    }

    /**
     * @test
     */
    public function dequeueReturnsFalseWhenQueueEmpty()
    {
        $this->adapter->
            expects($this->any())
            ->method('dequeue')
            ->will($this->returnValue(false));

        $this->assertFalse($this->queue->dequeue());
    }


    /**
     * @test
     */
    public function ackDelegates()
    {
        $message = Message::create('test-message', array('aybabtu' => 'lussentus'));
        $this->adapter->expects($this->once())->method('ack')->will($this->returnValue('luslus'));

        $this->ed
            ->expects($this->once())
            ->method('dispatch')
            ->with(Events::ACK, $this->isInstanceOf('Pekkis\Queue\MessageEvent'));

        $this->assertSame('luslus', $this->queue->ack($message));
    }

    /**
     * @test
     */
    public function purgeDelegates()
    {
        $this->ed
            ->expects($this->once())
            ->method('dispatch')
            ->with(Events::PURGE);

        $this->adapter->expects($this->once())->method('purge')->will($this->returnValue(true));
        $this->assertTrue($this->queue->purge());
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
        $subscriber = $this->getMock('Symfony\Component\EventDispatcher\EventSubscriberInterface');
        $this->ed->expects($this->once())->method('addSubscriber')->with($subscriber);

        $ret = $this->queue->addSubscriber($subscriber);
        $this->assertSame($this->queue, $ret);

    }

    /**
     * @test
     */
    public function unknownDataThrowsExceptionWhenSerializing()
    {
        $this->setExpectedException('RuntimeException', 'Serializer not found');

        $message = Message::create('lus.tus', new RandomBusinessObject());

        $this->queue->enqueue($message);
    }

    /**
     * @test
     */
    public function unknownDataThrowsExceptionWhenUnserializing()
    {
        $this->setExpectedException('RuntimeException', 'Unserializer not found');

        $message = Message::create('lus.tus', new RandomBusinessObject());

        $serialized = new SerializedData('SomeRandomSerializer', 'xooxoo');

        $arr = array(
            'uuid' => 'uuid',
            'type' => 'lus.tus',
            'data' => $serialized->toJson()
        );
        $json = json_encode($arr);

        $this->adapter->
            expects($this->once())
            ->method('dequeue')
            ->will($this->returnValue(array($json, 'aybabtu')));

        $this->queue->dequeue();
    }

    /**
     * @test
     */
    public function addsOutputFilter()
    {
        $ret = $this->queue->addOutputFilter(function () { });
        $this->assertSame($this->queue, $ret);
    }

    /**
     * @test
     */
    public function addsInputFilter()
    {
        $ret = $this->queue->addInputFilter(function () { });
        $this->assertSame($this->queue, $ret);
    }
}
