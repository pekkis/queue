<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue;

use Pekkis\Queue\Adapter\Adapter;
use Pekkis\Queue\Data\BasicDataSerializer;
use Pekkis\Queue\Data\DataSerializer;
use Pekkis\Queue\Data\DataSerializers;
use Pekkis\Queue\Data\SerializedData;
use Pekkis\Queue\Filter\InputFilters;
use Pekkis\Queue\Filter\OutputFilters;
use Closure;
use Pekkis\Queue\RuntimeException;

class Queue implements QueueInterface
{
    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var DataSerializers
     */
    private $dataSerializers;

    /**
     * @var OutputFilters
     */
    private $outputFilters;

    /**
     * @var InputFilters
     */
    private $inputFilters;

    /**
     * @param Adapter $adapter
     */
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->dataSerializers = new DataSerializers();

        $this->outputFilters = new OutputFilters();
        $this->inputFilters = new InputFilters();

        $this
            ->addDataSerializer(new BasicDataSerializer());
    }

    /**
     * @param string $topic
     * @param mixed $data
     * @return Message
     * @throws RuntimeException
     */
    public function enqueue($topic, $data = null)
    {
        $serializer = $this->dataSerializers->getSerializerFor($data);
        if (!$serializer) {
            throw new RuntimeException("Serializer not found");
        }
        $serializedData = new SerializedData($serializer->getIdentifier(), $serializer->serialize($data));

        $message = Message::create($topic, $data);
        $arr = array(
            'uuid' => $message->getUuid(),
            'topic' => $message->getTopic(),
            'data' => $serializedData->toJson()
        );

        $json = json_encode($arr);
        $json = $this->outputFilters->filter($json);

        $this->adapter->enqueue($json, $topic);
        return $message;
    }

    /**
     * Dequeues message
     *
     * @return Message
     */
    public function dequeue()
    {
        $raw = $this->adapter->dequeue();
        if (!$raw) {
            return false;
        }

        list ($json, $identifier, $internals) = $raw;
        $json = $this->inputFilters->filter($json);
        $json = json_decode($json, true);
        $serialized = SerializedData::fromJson($json['data']);
        $serializer = $this->dataSerializers->getUnserializerFor($serialized);

        if (!$serializer) {
            throw (new RuntimeException('Unserializer not found'))->setContext($raw);
        }

        $json['data'] = $serializer->unserialize($serialized->getData());
        $message = Message::fromArray($json);
        $message->setIdentifier($identifier);

        foreach ($internals as $key => $value) {
            $message->setInternal($key, $value);
        }

        return $message;
    }

    /**
     * Purges the queue
     */
    public function purge()
    {
        return $this->adapter->purge();
    }

    /**
     * Acknowledges message
     *
     * @param Message $message
     */
    public function ack(Message $message)
    {
        return $this->adapter->ack($message->getIdentifier(), $message->getInternals());
    }

    /**
     * @param DataSerializer $dataSerializer
     * @return Queue
     */
    public function addDataSerializer(DataSerializer $dataSerializer)
    {
        $this->dataSerializers->add($dataSerializer);
        return $this;
    }

    /**
     * @param callback $callable
     */
    public function addOutputFilter(Closure $callable)
    {
        $this->outputFilters->add($callable);
        return $this;
    }

    /**
     * @param callback $callable
     */
    public function addInputFilter(Closure $callable)
    {
        $this->inputFilters->add($callable);
        return $this;
    }

    /**
     * @return Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
}
