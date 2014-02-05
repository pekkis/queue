<?php

namespace Pekkis\Queue\Adapter;

use Aws\Sqs\SqsClient;
use Guzzle\Service\Resource\Model;
use Pekkis\Queue\Message;

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

        $this->client = SqsClient::factory(array(
            'key'    => $key,
            'secret' => $secret,
            'region' => $region,
        ));

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
            return null;
        }

        $message = Message::fromArray(json_decode($messages[0]['Body'], true));
        $message->setIdentifier($messages[0]['ReceiptHandle']);
        return $message;
    }

    public function enqueue(Message $message)
    {
        $this->client->sendMessage(
            array(
                'QueueUrl'    => $this->queueUrl,
                'MessageBody' => json_encode($message->toArray()),
            )
        );
    }

    public function purge()
    {
        while ($message = $this->dequeue()) {
            $this->ack($message);
        }
    }

    public function ack(Message $message)
    {
        $this->client->deleteMessage(
            array(
                'QueueUrl' => $this->queueUrl,
                'ReceiptHandle' => $message->getIdentifier(),
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
