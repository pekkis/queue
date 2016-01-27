<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\SymfonyBridge;

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
            sprintf("Message '%s' of topic '%s' enqueued", $message->getUuid(), $message->getTopic())
        );
    }

    public function onDequeue(MessageEvent $event)
    {
        $message = $event->getMessage();

        $this->output->writeln(
            sprintf("Message '%s' of topic '%s' dequeued", $message->getUuid(), $message->getTopic())
        );
    }

    public function onAck(MessageEvent $event)
    {
        $message = $event->getMessage();
        $this->output->writeln(
            sprintf("Message '%s' of topic '%s' acked", $message->getUuid(), $message->getTopic())
        );
    }

    public function onPurge(Event $event)
    {
        $this->output->writeln("Queue purged");
    }
}
