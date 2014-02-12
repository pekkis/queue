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
        $this->ed = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        $queue = $this
            ->getMockBuilder('Pekkis\Queue\SymfonyBridge\EventDispatchingQueue')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queue = $queue;
        $this->queue->expects($this->any())->method('getEventDispatcher')->will($this->returnValue($this->ed));

        $this->processor = new Processor($this->queue);
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
        $mockHandler
            ->expects($this->once())
            ->method('willHandle')
            ->with($message)
            ->will($this->returnValue(false));

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
    public function messagesAreHandled($successfulResult)
    {
        $message = Message::create('test', array('banana' => 'is not just a banaana, banaana'));

        $this->queue->expects($this->once())->method('dequeue')->will($this->returnValue($message));

        $mockHandler2 = $this->getMock('Pekkis\Queue\Processor\MessageHandler');
        $mockHandler2->expects($this->never())->method('willHandle');

        $mockHandler = $this->getMock('Pekkis\Queue\Processor\MessageHandler');
        $mockHandler
            ->expects($this->once())
            ->method('willHandle')
            ->with($message)
            ->will($this->returnValue(true));

        $message2 = Message::create('test', array('banana' => 'is not just a banaana, banaana'));
        $message3 = Message::create('test', array('banana' => 'is not just a banaana, banaana'));

        $result = new Result($successfulResult);

        $mockHandler
            ->expects($this->once())
            ->method('handle')
            ->with($message, $this->queue)
            ->will($this->returnValue($result));

        if ($successfulResult) {
            $this->queue->expects($this->once())->method('ack')->with($message);
        } else {
            $this->queue->expects($this->never())->method('ack');
        }

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

    /**
     * @test
     */
    public function processWhileProcessesUntilCallbackReturnsFalse()
    {
        $processor = $this->getMockBuilder('Pekkis\Queue\Processor\Processor')
            ->disableOriginalConstructor()
            ->setMethods(array('process'))
            ->getMock();

        $processor->expects($this->exactly(100))->method('process')->will($this->returnValue(true));

        $processor->processWhile(
            function () {
                static $count = 0;
                $count ++;

                if ($count >= 100) {
                    return false;
                }
                return true;
            }
        );
    }
}
