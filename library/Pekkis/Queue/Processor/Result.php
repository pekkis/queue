<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Processor;

class Result
{
    /**
     * @var bool
     */
    private $success;

    /**
     * @var string
     */
    private $resultMessage = '';

    public function __construct($success = true, $resultMessage = '')
    {
        $this->success = $success;
        $this->resultMessage = $resultMessage;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getResultMessage()
    {
        return $this->resultMessage;
    }
}
