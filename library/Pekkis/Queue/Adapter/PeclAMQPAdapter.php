<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Adapter;

use AMQPConnection;
use AMQPChannel;
use AMQPExchange;
use AMQPQueue;

class PeclAMQPAdapter implements Adapter
{
    private $conn;

    /**
     *
     * @var AMQPChannel
     */
    private $channel;

    private $exchange;

    private $queue;

    private $routingKey;

    public function __construct(
        $host,
        $port,
        $login,
        $password,
        $vhost,
        $exchangeName,
        $queueName,
        $exchangeType = AMQP_EX_TYPE_DIRECT,
        $routingKey = ''
    ) {

        $this->routingKey = $routingKey;

        $conn = new AMQPConnection(
            array(
                'host' => $host,
                'port' => $port,
                'vhost' => $vhost,
                'login' => $login,
                'password' => $password
            )
        );

        $conn->connect();

        $channel = new AMQPChannel($conn);

        $exchange = new AMQPExchange($channel);
        $exchange->setName($exchangeName);
        $exchange->setType($exchangeType);
        $exchange->setFlags(AMQP_DURABLE);

        $exchange->declareExchange();

        $queue = new AMQPQueue($channel);
        $queue->setName($queueName);
        $queue->setFlags(AMQP_DURABLE);
        $queue->bind($exchangeName, $routingKey);
        $queue->declareQueue();

        $this->conn = $conn;
        $this->exchange = $exchange;
        $this->channel = $channel;
        $this->queue = $queue;
    }

    public function enqueue($msg, $topic)
    {
        if ($this->routingKey) {
            $routingKey = $topic;
        } else {
            $routingKey = '';
        }

        $this->exchange->publish($msg, $routingKey);
    }

    public function dequeue()
    {
        $msg = $this->queue->get();
        if (!$msg) {
            return false;
        }

        return array(
            $msg->getBody(),
            $msg->getDeliveryTag(),
            []
        );
    }

    public function purge()
    {
        return $this->queue->purge();
    }

    public function ack($identifier, $internals)
    {
        $this->queue->ack($identifier);
    }

    public function __destruct()
    {
        $this->conn->disconnect();
    }
}
