<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Adapter;

interface Adapter
{
    /**
     * Enqueues a message
     *
     * @param string $message
     */
    public function enqueue($message);

    /**
     * Dequeues a message
     *
     * @return array
     */
    public function dequeue();

    /**
     * Purges the queue
     */
    public function purge();

    /**
     * Acknowledges a message
     *
     * @param mixed $identifier
     */
    public function ack($identifier);
}
