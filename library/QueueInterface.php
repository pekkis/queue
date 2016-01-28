<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue;

use Pekkis\Queue\Adapter\Adapter;

Interface QueueInterface
{
    /**
     * @param string $topic
     * @param mixed $data
     * @return Message
     * @throws RuntimeException
     */
    public function enqueue($topic, $data = null);

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

    /**
     * @return Adapter
     */
    public function getAdapter();
}
