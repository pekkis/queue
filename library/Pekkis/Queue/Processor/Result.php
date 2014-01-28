<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Processor;

use Pekkis\Queue\Enqueueable;

class Result
{
    private $success;

    private $resultMessage = '';

    private $messages = array();

    public function __construct($success = true, $resultMessage = '')
    {
        $this->success = $success;
        $this->resultMessage = $resultMessage;
    }

    public function addMessage(Enqueueable $enqueueable)
    {
        $this->messages[] = $enqueueable->getMessage();
    }

    public function isSuccess()
    {
        return $this->success;
    }

    public function getResultMessage()
    {
        return $this->resultMessage;
    }

    public function getMessages()
    {
        return $this->messages;
    }

}
