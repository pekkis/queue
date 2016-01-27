<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\SymfonyBridge;

use Pekkis\Queue\QueueInterface;
use Pekkis\Queue\Message;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Pekkis\Queue\Adapter\Adapter;

class EventDispatchingQueue implements QueueInterface
{
    /**
     * @var QueueInterface
     */
    private $queue;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(QueueInterface $queue, EventDispatcherInterface $eventDispatcher)
    {
        $this->queue = $queue;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $topic
     * @param null $data
     * @return Message
     */
    public function enqueue($topic, $data = null)
    {
        $message = $this->queue->enqueue($topic, $data);
        $this->eventDispatcher->dispatch(Events::ENQUEUE, new MessageEvent($message));

        return $message;
    }

    /**
     * @return Message
     */
    public function dequeue()
    {
        $message = $this->queue->dequeue();

        if ($message) {
            $this->eventDispatcher->dispatch(Events::DEQUEUE, new MessageEvent($message));
        }

        return $message;
    }

    /**
     * @return bool
     */
    public function purge()
    {
        $ret = $this->queue->purge();
        $this->eventDispatcher->dispatch(Events::PURGE);

        return $ret;
    }

    public function ack(Message $message)
    {
        $ret = $this->queue->ack($message);
        $this->eventDispatcher->dispatch(Events::ACK, new MessageEvent($message));

        return $ret;
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
     * @return EventDispatchingQueue
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->eventDispatcher->addSubscriber($subscriber);
        return $this;
    }

    /**
     * @return Adapter
     */
    public function getAdapter()
    {
        return $this->queue->getAdapter();
    }

}
