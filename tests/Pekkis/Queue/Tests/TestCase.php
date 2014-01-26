<?php

namespace Pekkis\Queue\Tests;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function assertUuid($what)
    {
        $this->assertRegexp(
            '/([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/',
            $what,
            "'{$what}' is not an UUID"
        );
    }
}
