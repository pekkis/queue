<?php

namespace Pekkis\Queue\Tests\Adapter;

class AdapterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function interfaceShouldExist()
    {
        $this->assertTrue(interface_exists('Pekkis\Queue\Adapter\Adapter'));
    }
}
