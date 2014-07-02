<?php

namespace Pekkis\Queue\Tests\Adapter;

use Pekkis\Queue\Adapter\AmazonSQSAdapter;

class AmazonSQSAdapterTest extends TestCase
{
    protected function getAdapter()
    {
        return new AmazonSQSAdapter(
            getenv('SQS_KEY'),
            getenv('SQS_SECRET'),
            SQS_REGION,
            SQS_QUEUE_NAME,
            3
        );
    }

    public function setUp()
    {
        if (!getenv('SQS_KEY') || !getenv('SQS_SECRET') || !SQS_REGION || !SQS_QUEUE_NAME) {
            $this->markTestSkipped("SQS credentials not configured");
        }
        parent::setUp();
    }

    protected function getSleepyTime()
    {
        return 6;
    }
}
