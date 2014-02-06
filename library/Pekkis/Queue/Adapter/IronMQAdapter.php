<?php

namespace Pekkis\Queue\Adapter;

use IronMQ;
use Pekkis\Queue\Message;

/**
 * IronMQ queue
 */
class IronMQAdapter implements Adapter
{
    /**
     * @var IronMQ
     */
    private $queue;

    /**
     * @var string
     */
    private $queueName;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var int
     */
    private $expiresIn;

    /**
     * @param string $token
     * @param string $projectId
     * @param string $queueName
     * @param int $timeout
     * @param int $expiresIn
     */
    public function __construct($token, $projectId, $queueName, $timeout = 60, $expiresIn = 604800)
    {
        $this->queue = new IronMQ(
            array(
                'token' => $token,
                'project_id' => $projectId
            )
        );
        $this->queueName = $queueName;
        $this->setTimeout($timeout);
        $this->setExpiresIn($expiresIn);
    }

    /**
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $expiresIn
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;
    }

    /**
     * @return int
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @param Message $msg
     * @return bool
     */
    public function enqueue(Message $msg)
    {
        try {
            $this->queue->postMessage(
                $this->queueName,
                json_encode($msg->toArray()),
                array(
                    'timeout' => $this->timeout,
                    'expires_in' => $this->expiresIn
                )
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function dequeue()
    {
        $rawMsg = $this->queue->getMessage($this->queueName, $this->timeout);
        if (!$rawMsg) {
            return null;
        }

        $msg = Message::fromArray(json_decode($rawMsg->body, true));
        $msg->setIdentifier($rawMsg->id);
        return $msg;
    }

    public function purge()
    {
        try {
            $this->queue->clearQueue($this->queueName);
        } catch (\Http_Exception $e) {
            // Queue is not found, so it's as good as purged, no?
            if ($e->getCode() !== 404) {
                throw $e;
            }
            return true;
        }
    }

    public function ack(Message $message)
    {
        $this->queue->deleteMessage($this->queueName, $message->getIdentifier());
    }
}
