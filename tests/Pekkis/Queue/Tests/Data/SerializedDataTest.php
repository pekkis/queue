<?php

namespace Pekkis\Queue\Data;

use Pekkis\Queue\RuntimeException;
use PHPUnit\Framework\TestCase;

class SerializedDataTest extends TestCase
{
    /**
     * @test
     *
     */
    public function failsWhenDataCannotBeEncoded()
    {
        $this->expectException(RuntimeException::class);
        $serializedData = new SerializedData('tussenhofer', utf8_decode('söösöö'));
        $serializedData->toJson();
    }
}
