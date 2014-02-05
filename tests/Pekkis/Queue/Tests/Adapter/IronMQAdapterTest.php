<?php

namespace Pekkis\Queue\Tests\Adapter;

use Pekkis\Queue\Adapter\IronMQAdapter;

class IronMQAdapterTest extends TestCase
{

    protected function getAdapter()
    {
        return new IronMQAdapter(
            IRONMQ_TOKEN,
            IRONMQ_PROJECT_ID,
            'pekkis-queue-test',
            3
        );
    }

    public function setUp()
    {
        if (!IRONMQ_TOKEN || !IRONMQ_PROJECT_ID) {
            $this->markTestSkipped("IronMQ credentials not configured");
        }
        parent::setUp();
    }

    protected function getSleepyTime()
    {
        return 6;
    }


}
