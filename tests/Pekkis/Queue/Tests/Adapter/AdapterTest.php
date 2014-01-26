<?php

namespace Pekkis\Queue\Tests\Adapter;

class AdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function interfaceShouldExist()
    {
        $this->assertTrue(interface_exists('Pekkis\Queue\Adapter\Adapter'));
    }

}
