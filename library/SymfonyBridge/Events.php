<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\SymfonyBridge;

class Events
{
    const ENQUEUE = 'pekkis_queue.enqueue';
    const DEQUEUE = 'pekkis_queue.dequeue';
    const ACK = 'pekkis_queue.ack';
    const PURGE = 'pekkis_queue.purge';
}
