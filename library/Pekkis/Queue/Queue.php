<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue;

use Pekkis\Queue\Adapter\Adapter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Queue
{
    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param Adapter $adapter
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(Adapter $adapter, EventDispatcherInterface $eventDispatcher)
    {
        $this->adapter = $adapter;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Enqueues message
     *
     * @param Enqueueable $enqueueable
     */
    public function enqueue(Enqueueable $enqueueable)
    {
        $message = $enqueueable->getMessage();
        $this->eventDispatcher->dispatch(Events::ENQUEUE, new MessageEvent($message));
        return $this->adapter->enqueue($message);
    }

    /**
     * Dequeues message
     *
     * @return Message
     */
    public function dequeue()
    {
        $message = $this->adapter->dequeue();
        if ($message) {
            $this->eventDispatcher->dispatch(Events::DEQUEUE, new MessageEvent($message));
        }
        return $message;
    }

    /**
     * Purges the queue
     */
    public function purge()
    {
        $this->eventDispatcher->dispatch(Events::PURGE);
        return $this->adapter->purge();
    }

    /**
     * Acknowledges message
     *
     * @param Message $message
     */
    public function ack(Message $message)
    {
        $this->eventDispatcher->dispatch(Events::ACK, new MessageEvent($message));
        return $this->adapter->ack($message);
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @param EventSubscriberInterface $subscriber
     * @return Queue
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->eventDispatcher->addSubscriber($subscriber);
        return $this;
    }
}
