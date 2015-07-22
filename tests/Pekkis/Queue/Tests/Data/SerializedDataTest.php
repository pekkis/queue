<?php

namespace Pekkis\Queue\Data;

class SerializedDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     */
    public function failsWhenDataCannotBeEncoded()
    {
        $this->setExpectedException('\RuntimeException');
        $serializedData = new SerializedData('tussenhofer', utf8_decode('söösöö'));
        $serializedData->toJson();
    }
}
