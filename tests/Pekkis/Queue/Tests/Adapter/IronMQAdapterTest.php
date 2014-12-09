<?php

namespace Pekkis\Queue\Tests\Adapter;

use Pekkis\Queue\Adapter\IronMQAdapter;

class IronMQAdapterTest extends TestCase
{
    public function setUp()
    {
        if (!getenv('IRONMQ_TOKEN') || !getenv('IRONMQ_PROJECT_ID')) {
            $this->markTestSkipped("IronMQ credentials not configured");
        }

        parent::setUp();
    }

    protected function getAdapter()
    {
        return new IronMQAdapter(
            getenv('IRONMQ_TOKEN'),
            getenv('IRONMQ_PROJECT_ID'),
            'pekkis-queue-test',
            3,
            604800,
            getenv('IRONMQ_HOST') ?: null
        );
    }

    protected function getSleepyTime()
    {
        return 6;
    }
}
