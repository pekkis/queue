<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Adapter;

use Pekkis\Queue\Enqueueable;
use Pekkis\Queue\Message;

interface Adapter
{
    /**
     * Enqueues message
     *
     * @param Enqueueable $enqueueable
     */
    public function enqueue(Enqueueable $enqueueable);

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
