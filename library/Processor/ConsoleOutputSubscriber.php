<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Processor;

use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Pekkis\Queue\SymfonyBridge\MessageEvent;

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
            Events::QUEUE_EMPTY => 'onQueueEmpty',
            Events::MESSAGE_RECEIVE => 'onReceive',
            Events::MESSAGE_NOT_HANDLABLE => 'onNotHandlable',
            Events::MESSAGE_BEFORE_HANDLE => 'beforeHandle',
            Events::MESSAGE_AFTER_HANDLE => 'afterHandle',
        );
    }

    public function onReceive(MessageEvent $event)
    {
        $message = $event->getMessage();
        $this->output->writeln(
            sprintf("Message '%s' of topic '%s' received by processor", $message->getUuid(), $message->getTopic())
        );
    }

    public function onNotHandlable(MessageEvent $event)
    {
        $message = $event->getMessage();
        $this->output->writeln(
            sprintf("Message '%s' of topic '%s' is not handlable", $message->getUuid(), $message->getTopic())
        );
    }

    public function beforeHandle(MessageEvent $event)
    {
        $message = $event->getMessage();
        $this->output->writeln(
            sprintf("Message '%s' of topic '%s' will be handled", $message->getUuid(), $message->getTopic())
        );
    }

    public function afterHandle(ResultEvent $event)
    {
        $message = $event->getMessage();
        $result = $event->getResult();

        $successStr = $result->isSuccess() ? 'SUCCESS': 'FAILURE';

        $this->output->writeln(
            sprintf(
                "Message '%s' of topic '%s' was handled: %s. Result message: '%s'",
                $message->getUuid(),
                $message->getTopic(),
                $successStr,
                $result->getResultMessage()
            )
        );
    }

    public function onQueueEmpty(Event $event)
    {
        $this->output->writeln("Queue is empty");
    }
}
