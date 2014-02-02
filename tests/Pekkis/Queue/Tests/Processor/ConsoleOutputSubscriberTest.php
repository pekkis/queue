<?php

namespace Pekkis\Queue\Processor\Tests;

use Pekkis\Queue\Processor\ConsoleOutputSubscriber;
use Pekkis\Queue\Processor\Events;
use Pekkis\Queue\MessageEvent;
use Pekkis\Queue\Message;
use Pekkis\Queue\Processor\Result;
use Pekkis\Queue\Processor\ResultEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ConsoleOutputSubscriberTest extends \Pekkis\Queue\Tests\TestCase
{

    /**
     * @var EventDispatcher
     */
    private $ed;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $output;

    /**
     * @var Message
     */
    private $message;

    public function setUp()
    {
        $this->message = Message::create('lussuti.lus');

        $this->ed = new EventDispatcher();

        $this->output = $this->getMock('Symfony\Component\Console\Output\ConsoleOutputInterface');
        $subscriber = new ConsoleOutputSubscriber($this->output);
        $this->ed->addSubscriber($subscriber);

    }


    public function provideMessageEvents()
    {
        return array(
            array(Events::MESSAGE_RECEIVE, 'receive'),
            array(Events::MESSAGE_NOT_HANDLABLE, 'not handlable'),
            array(Events::MESSAGE_BEFORE_HANDLE, 'will be handled'),
        );
    }

    /**
     * @test
     * @dataProvider provideMessageEvents
     */
    public function listensToMessageEvents($eventName, $expectedToContain)
    {
        $self = $this;
        $message = $this->message;

        $this->output
            ->expects($this->once())
            ->method('writeln')
            ->will(
                $this->returnCallback(
                    function ($output) use ($self, $message, $expectedToContain) {

                        $self->assertContains($message->getUuid(), $output);
                        $self->assertContains($message->getType(), $output);
                        $self->assertContains($expectedToContain, $output);

                    }
                )
            );

        $event = new MessageEvent($this->message);
        $this->ed->dispatch($eventName, $event);
    }

    /**
     * @test
     */
    public function listensToQueueEmpty()
    {
        $self = $this;
        $message = $this->message;

        $this->output
            ->expects($this->once())
            ->method('writeln')
            ->will(
                $this->returnCallback(
                    function ($output) use ($self, $message) {
                        $self->assertContains('empty', $output);
                    }
                )
            );

        $event = new Event();
        $this->ed->dispatch(Events::QUEUE_EMPTY, $event);
    }

    public function provideResults()
    {
        return array(
            array(new Result(true, 'lussuti')),
            array(new Result(false, 'mussutusta')),
        );
    }

    /**
     * @test
     * @dataProvider provideResults
     */
    public function listensToResults(Result $result)
    {
        $self = $this;
        $message = $this->message;

        $this->output
            ->expects($this->once())
            ->method('writeln')
            ->will(
                $this->returnCallback(
                    function ($output) use ($self, $message, $result) {

                        $successStr = $result->isSuccess() ? 'SUCCESS': 'FAILURE';
                        $self->assertContains($message->getUuid(), $output);
                        $self->assertContains($message->getType(), $output);
                        $self->assertContains($successStr, $output);
                        $self->assertContains($result->getResultMessage(), $output);
                    }
                )
            );

        $event = new ResultEvent($result, $this->message);
        $this->ed->dispatch(Events::MESSAGE_AFTER_HANDLE, $event);
    }



}
