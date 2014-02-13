<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue;

Interface QueueInterface
{
    /**
     * @param string $type
     * @param mixed $data
     * @return Message
     */
    public function enqueue($type, $data = null);

    /**
     * Dequeues message
     *
     * @return Message
     */
    public function dequeue();

    /**
     * Purges the queue
     */
    public function purge();

    /**
     * Acknowledges message
     *
     * @param Message $message
     */
    public function ack(Message $message);
}
