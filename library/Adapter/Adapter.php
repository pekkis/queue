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
     * @param string $topic
     */
    public function enqueue($message, $topic);

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
     * @param array $internals
     */
    public function ack($identifier, $internals);
}
