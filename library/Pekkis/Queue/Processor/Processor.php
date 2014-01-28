<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Processor;

use Pekkis\Queue\MessageEvent;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Pekkis\Queue\Queue;
use Pekkis\Queue\Message;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default implementation of a queue processor
 */
class Processor
{
    /**
     *
     * @var Queue
     */
    protected $queue;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var MessageHandler[]
     */
    protected $handlers = array();

    public function __construct(Queue $queue, EventDispatcherInterface $eventDispatcher)
    {
        $this->queue = $queue;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getQueue()
    {
        return $this->queue;
    }

    public function registerHandler(MessageHandler $handler)
    {
        array_unshift($this->handlers, $handler);
    }

    /**
     * Processes a single message from the queue
     *
     * @return boolean False if there are no messages in the queue.
     */
    public function process()
    {
        $message = $this->queue->dequeue();

        if (!$message) {
            $this->eventDispatcher->dispatch(Events::NOTHING_TO_PROCESS);
            return false;
        }

        $this->eventDispatcher->dispatch(Events::MESSAGE_RECEIVE, new MessageEvent($message));

        $result = $this->handleMessage($message);
        if (!$result) {

            $this->eventDispatcher->dispatch(Events::MESSAGE_NOT_HANDLABLE, new MessageEvent($message));
            throw new \RuntimeException(sprintf("No handler will handle a message of type '%s'", $message->getType()));
        }

        if ($result->isSuccess()) {
            $this->queue->ack($message);
        }
        foreach ($result->getMessages() as $message) {
            $this->queue->enqueue($message);
        }

        return true;
    }

    /**
     * @param Message $message
     * @return Result
     */
    private function handleMessage(Message $message)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->willHandle($message)) {
                $this->eventDispatcher->dispatch(Events::MESSAGE_BEFORE_HANDLE, new MessageEvent($message));

                $ret = $handler->handle($message);

                $this->eventDispatcher->dispatch(
                    Events::MESSAGE_AFTER_HANDLE,
                    new ResultEvent($ret, $message)
                );

                return $ret;
            }
        }
        return false;
    }
}
