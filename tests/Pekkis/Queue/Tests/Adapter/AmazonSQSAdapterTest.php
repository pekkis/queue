<?php

namespace Pekkis\Queue\Tests\Adapter;

use Pekkis\Queue\Adapter\AmazonSQSAdapter;

class AmazonSQSAdapterTest extends TestCase
{

    protected function getAdapter()
    {
        return new AmazonSQSAdapter(
            SQS_KEY,
            SQS_SECRET,
            SQS_REGION,
            'pekkis-queue-test',
            3
        );
    }

    public function setUp()
    {
        if (!SQS_KEY || !SQS_SECRET || !SQS_REGION) {
            $this->markTestSkipped("SQS credentials not configured");
        }
        parent::setUp();
    }

    protected function getSleepyTime()
    {
        return 6;
    }


}
