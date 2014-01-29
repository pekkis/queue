<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Adapter;

use Pekkis\Queue\Message;

interface Adapter
{
    /**
     * Enqueues a message
     *
     * @param Message $message
     */
    public function enqueue(Message $message);

    /**
     * Dequeues a message
     *
     * @return Message
     */
    public function dequeue();

    /**
     * Purges the queue
     */
    public function purge();

    /**
     * Acknowledges a message
     *
     * @param Message $message
     */
    public function ack(Message $message);
}
