<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Adapter;

use Aws\Sqs\SqsClient;
use Guzzle\Service\Resource\Model;

class AmazonSQSAdapter implements Adapter
{
    /**
     * @var SqsClient
     */
    private $client;

    /**
     * @var string
     */
    private $queueName;

    /**
     * @var string
     */
    private $queueUrl;

    /**
     * @var int
     */
    private $visibilityTimeout;

    public function __construct($key, $secret, $region, $queueName, $visibilityTimeout = 60)
    {
        $this->queueName = $queueName;
        $this->visibilityTimeout = $visibilityTimeout;

        $this->client = SqsClient::factory(
            array(
                'key'    => $key,
                'secret' => $secret,
                'region' => $region,
            )
        );

        $this->createQueue();
    }

    public function dequeue()
    {
        /** @var Model $result */
        $result = $this->client->receiveMessage(
            array(
                'QueueUrl'        => $this->queueUrl,
            )
        );

        $messages = $result->get('Messages');
        if (!$messages) {
            return false;
        }

        return array($messages[0]['Body'], $messages[0]['ReceiptHandle']);
    }

    public function enqueue($message)
    {
        $this->client->sendMessage(
            array(
                'QueueUrl'    => $this->queueUrl,
                'MessageBody' => $message,
            )
        );
    }

    public function purge()
    {
        while ($dequeued = $this->dequeue()) {
            list ($message, $identifier) = $dequeued;
            $this->ack($identifier);
        }
    }

    public function ack($identifier)
    {
        $this->client->deleteMessage(
            array(
                'QueueUrl' => $this->queueUrl,
                'ReceiptHandle' => $identifier,
            )
        );
    }

    private function createQueue()
    {
        $result = $this->client->createQueue(
            array(
                'QueueName' => $this->queueName,
            )
        );
        $this->queueUrl = $result->get('QueueUrl');

        $this->client->setQueueAttributes(
            array(
                'QueueUrl' => $this->queueUrl,
                'Attributes' => array(
                    'VisibilityTimeout' => $this->visibilityTimeout,
                ),
            )
        );
    }
}
