<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Processor;

class Events
{
    const NOTHING_TO_PROCESS = 'pekkis_queue.processor.nothing_to_process';
    const MESSAGE_RECEIVE = 'pekkis_queue.processor.message.receive';
    const MESSAGE_NOT_HANDLABLE = 'pekkis_queue.processor.message.not_handlable';
    const MESSAGE_BEFORE_HANDLE = 'pekkis_queue.processor.message.before_handle';
    const MESSAGE_AFTER_HANDLE = 'pekkis_queue.processor.message.after_handle';
}
