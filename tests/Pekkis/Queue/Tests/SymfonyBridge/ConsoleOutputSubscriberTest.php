<?php

namespace Pekkis\Queue\Tests;

use Pekkis\Queue\SymfonyBridge\ConsoleOutputSubscriber;
use Pekkis\Queue\SymfonyBridge\Events;
use Pekkis\Queue\SymfonyBridge\MessageEvent;
use Pekkis\Queue\Message;
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
            array(Events::DEQUEUE, 'dequeue'),
            array(Events::ENQUEUE, 'enqueue'),
            array(Events::ACK, 'ack'),
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
    public function listensToPurge()
    {
        $self = $this;
        $message = $this->message;

        $this->output
            ->expects($this->once())
            ->method('writeln')
            ->will(
                $this->returnCallback(
                    function ($output) use ($self, $message) {
                        $self->assertContains('purge', $output);
                    }
                )
            );

        $event = new Event();
        $this->ed->dispatch(Events::PURGE, $event);
    }
}
