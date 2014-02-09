<?php

namespace Pekkis\Queue\Tests\Processor;

use Pekkis\Queue\Processor\Result;

class ResultTest extends \Pekkis\Queue\Tests\TestCase
{

    /**
     * @test
     */
    public function instantiates()
    {
        $msg = 'All your base are belong to us';
        $result = new Result(false, $msg);
        $this->assertSame($msg, $result->getResultMessage());
        $this->assertFalse($result->isSuccess());
    }
}

