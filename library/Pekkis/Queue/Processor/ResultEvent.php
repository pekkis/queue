<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Processor;

use Pekkis\Queue\MessageEvent;
use Pekkis\Queue\Message;

class ResultEvent extends MessageEvent
{
    /**
     * @var Result
     */
    protected $result;

    /**
     * @param Result $result
     * @param Message $message
     */
    public function __construct(Result $result, Message $message)
    {
        parent::__construct($message);
        $this->result = $result;
    }

    /**
     * @return Result
     */
    public function getResult()
    {
        return $this->result;
    }
}
