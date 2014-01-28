<?php

namespace Pekkis\Queue\Tests\Processor;

use Pekkis\Queue\Processor\Result;
use Pekkis\Queue\Processor\Processor;
use Pekkis\Queue\Message;

class ProcessorTest extends \Pekkis\Queue\Tests\TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $queue;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $ed;

    /**
     * @var Processor
     */
    protected $processor;


    public function setUp()
    {
        $queue = $this->getMockBuilder('Pekkis\Queue\Queue')->disableOriginalConstructor()->getMock();
        $this->queue = $queue;
        $this->ed = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->processor = new Processor($this->queue, $this->ed);
    }

    /**
     * @test
     */
    public function getQueueReturnsQueue()
    {
        $this->assertSame($this->queue, $this->processor->getQueue());
    }

    /**
     * @test
     */
    public function exceptionIsThrownWhenNoHandlers()
    {
        $this->setExpectedException('RuntimeException', "No handler will handle a message of type 'test'");

        $message = Message::create('test', array('banana' => 'is not just a banaana, banaana'));

        $this->queue->expects($this->once())->method('dequeue')->will($this->returnValue($message));

        $this->processor->process($message);
    }

    /**
     * @test
     *
     */
    public function exceptionIsThrownWhenNoHandlerWillHandleMessage()
    {
        $this->setExpectedException('RuntimeException', "No handler will handle a message of type 'test'");

        $message = Message::create('test', array('banana' => 'is not just a banaana, banaana'));

        $this->queue->expects($this->once())->method('dequeue')->will($this->returnValue($message));

        $mockHandler = $this->getMock('Pekkis\Queue\Processor\MessageHandler');
        $mockHandler->expects($this->once())->method('willHandle')->with($message)->will($this->returnValue(false));
        $mockHandler->expects($this->never())->method('handle');

        $this->processor->registerHandler($mockHandler);

        $this->processor->process($message);
    }

    public function provideData()
    {
        return array(
            array(true),
            array(false)
        );
    }

    /**
     * @test
     * @dataProvider provideData
     */
    public function newMessagesWillBeQueuedFromResponse($successfulResult)
    {
        $message = Message::create('test', array('banana' => 'is not just a banaana, banaana'));

        $this->queue->expects($this->once())->method('dequeue')->will($this->returnValue($message));

        $mockHandler2 = $this->getMock('Pekkis\Queue\Processor\MessageHandler');
        $mockHandler2->expects($this->never())->method('willHandle');

        $mockHandler = $this->getMock('Pekkis\Queue\Processor\MessageHandler');
        $mockHandler->expects($this->once())->method('willHandle')->with($message)->will($this->returnValue(true));

        $message2 = Message::create('test', array('banana' => 'is not just a banaana, banaana'));
        $message3 = Message::create('test', array('banana' => 'is not just a banaana, banaana'));

        $result = new Result($successfulResult);
        $result->addMessage($message2);
        $result->addMessage($message3);

        $mockHandler->expects($this->once())->method('handle')->will($this->returnValue($result));

        if ($successfulResult) {
            $this->queue->expects($this->once())->method('ack')->with($message);
        } else {
            $this->queue->expects($this->never())->method('ack');
        }

        $this->queue
            ->expects($this->exactly(2))
            ->method('enqueue')
            ->with($this->isInstanceOf('Pekkis\Queue\Message'));

        $this->processor->registerHandler($mockHandler2);
        $this->processor->registerHandler($mockHandler);

        $this->processor->process($message);
    }

    /**
     * @test
     */
    public function exitsEarlyWhenNoMessages()
    {
        $this->queue->expects($this->once())->method('dequeue')->will($this->returnValue(false));

        $ret = $this->processor->process();
        $this->assertFalse($ret);
    }
}
