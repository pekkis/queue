<?php

namespace Pekkis\Queue;

use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

class ConsoleOutputSubscriber implements EventSubscriberInterface
{
    /**
     * @var ConsoleOutputInterface
     */
    private $output;

    /**
     * @param ConsoleOutputInterface $output
     */
    public function __construct(ConsoleOutputInterface $output)
    {
        $this->output = $output;
    }

    public static function getSubscribedEvents()
    {
        return array(
            Events::ENQUEUE => 'onEnqueue',
            Events::DEQUEUE => 'onDequeue',
            Events::ACK => 'onAck',
            Events::PURGE => 'onPurge',
        );
    }

    public function onEnqueue(MessageEvent $event)
    {
        $message = $event->getMessage();
        $this->output->writeln(
            sprintf("Message '%' of type '%s' enqueued", $message->getUuid(), $message->getType())
        );
    }

    public function onDequeue(MessageEvent $event)
    {
        $message = $event->getMessage();
        $this->output->writeln(
            sprintf("Message '%' of type '%s' dequeued", $message->getUuid(), $message->getType())
        );
    }

    public function onAck(MessageEvent $event)
    {
        $message = $event->getMessage();
        $this->output->writeln(
            sprintf("Message '%' of type '%s' acked", $message->getUuid(), $message->getType())
        );
    }

    public function onPurge(Event $event)
    {
        $this->output->writeln("Queue purged");
    }
}
