<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Adapter;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;

class PhpAMQPAdapter implements Adapter
{
    /**
     *
     * @var AMQPChannel
     */
    private $channel;

    private $exchange;

    private $queue;

    /**
     * @var array
     */
    private $connectionOptions = array();

    public function __construct($host, $port, $user, $pass, $vhost, $exchange, $queue)
    {
        $this->exchange = $exchange;
        $this->queue = $queue;

        $this->connectionOptions = array(
            'host' => $host,
            'port' => $port,
            'user' => $user,
            'pass' => $pass,
            'vhost' => $vhost,
        );
    }

    /**
     * Connects to AMQP and gets channel
     *
     * @return AMQPChannel
     */
    private function getChannel()
    {
        if (!$this->channel) {
            $conn = new AMQPStreamConnection(
                $this->connectionOptions['host'],
                $this->connectionOptions['port'],
                $this->connectionOptions['user'],
                $this->connectionOptions['pass'],
                $this->connectionOptions['vhost']
            );
            $ch = $conn->channel();

            $ch->queue_declare($this->queue, false, true, false, false);
            $ch->exchange_declare($this->exchange, 'direct', false, true, false);
            $ch->queue_bind($this->queue, $this->exchange, '');
            $this->channel = $ch;
        }

        return $this->channel;
    }

    public function enqueue($msg)
    {
        $msg = new AMQPMessage(
            $msg,
            array('content_type' => 'text/plain', 'delivery-mode' => 1)
        );
        $this->getChannel()->basic_publish($msg, $this->exchange, '', false, false);
    }

    public function dequeue()
    {
        $msg = $this->getChannel()->basic_get($this->queue);

        if (!$msg) {
            return false;
        }

        return array(
            $msg->body,
            $msg->delivery_info['delivery_tag']
        );
    }

    public function purge()
    {
        return $this->getChannel()->queue_purge($this->queue);
    }

    public function ack($identifier)
    {
        $this->getChannel()->basic_ack($identifier);
    }
}
