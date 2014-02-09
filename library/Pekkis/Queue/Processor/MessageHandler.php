<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Processor;

use Pekkis\Queue\Message;
use Pekkis\Queue\Queue;

interface MessageHandler
{
    /**
     * @param Message $message
     * @return bool
     */
    public function willHandle(Message $message);

    /**
     * @param Message $message
     * @return Result
     */
    public function handle(Message $message, Queue $queue);
}
