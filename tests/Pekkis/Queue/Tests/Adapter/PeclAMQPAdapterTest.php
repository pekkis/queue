<?php

namespace Pekkis\Queue\Tests\Adapter;

use Pekkis\Queue\Adapter\PeclAMQPAdapter;

class PeclAMQPAdapterTest extends TestCase
{

    protected function getAdapter()
    {
        return new PeclAMQPAdapter(
            RABBITMQ_HOST,
            RABBITMQ_PORT,
            RABBITMQ_USERNAME,
            RABBITMQ_PASSWORD,
            RABBITMQ_VHOST,
            'test_exchange',
            'test_queue'
        );
    }

    public function setUp()
    {
        if (!class_exists("\AMQPConnection")) {
            $this->markTestSkipped("AMQP PECL extension required");
        }

        if (!RABBITMQ_HOST) {
            $this->markTestSkipped('RabbitMQ not configured');
        }
        parent::setUp();
    }
}
