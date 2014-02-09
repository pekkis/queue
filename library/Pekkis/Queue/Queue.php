<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue;

use Pekkis\Queue\Adapter\Adapter;
use Pekkis\Queue\Data\ArrayDataSerializer;
use Pekkis\Queue\Data\DataSerializer;
use Pekkis\Queue\Data\DataSerializers;
use Pekkis\Queue\Data\NullDataSerializer;
use Pekkis\Queue\Data\SerializedData;
use Pekkis\Queue\Data\StdClassDataSerializer;
use Pekkis\Queue\Data\StringDataSerializer;
use Pekkis\Queue\Filter\InputFilters;
use Pekkis\Queue\Filter\OutputFilters;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Closure;

class Queue
{
    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var DataSerializers
     */
    private $dataSerializers;

    private $outputFilters;

    private $inputFilters;

    /**
     * @param Adapter $adapter
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(Adapter $adapter, EventDispatcherInterface $eventDispatcher)
    {
        $this->adapter = $adapter;
        $this->eventDispatcher = $eventDispatcher;
        $this->dataSerializers = new DataSerializers();

        $this->outputFilters = new OutputFilters();
        $this->inputFilters = new InputFilters();

        $this
            ->addDataSerializer(new StdClassDataSerializer())
            ->addDataSerializer(new ArrayDataSerializer())
            ->addDataSerializer(new StringDataSerializer())
            ->addDataSerializer(new NullDataSerializer());
    }

    /**
     * Enqueues message
     *
     * @param Enqueueable $enqueueable
     */
    public function enqueue(Enqueueable $enqueueable)
    {
        $message = $enqueueable->getMessage();

        $data = $message->getData();
        $serializer = $this->dataSerializers->getSerializerFor($data);
        if (!$serializer) {
            throw new \RuntimeException("Serializer not found");
        }
        $serializedData = new SerializedData($serializer->getIdentifier(), $serializer->serialize($data));

        $arr = array(
            'uuid' => $message->getUuid(),
            'type' => $message->getType(),
            'data' => $serializedData->toJson()
        );

        $json = json_encode($arr);
        $this->eventDispatcher->dispatch(Events::ENQUEUE, new MessageEvent($message));
        $json = $this->outputFilters->filter($json);
        return $this->adapter->enqueue($json);
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

        list ($json, $identifier) = $raw;

        $json = $this->inputFilters->filter($json);

        $json = json_decode($json, true);

        $serialized = SerializedData::fromJson($json['data']);

        $serializer = $this->dataSerializers->getUnserializerFor($serialized);

        if (!$serializer) {
            throw new \RuntimeException('Unserializer not found');
        }

        $json['data'] = $serializer->unserialize($serialized->getData());

        $message = Message::fromArray($json);
        $message->setIdentifier($identifier);

        $this->eventDispatcher->dispatch(Events::DEQUEUE, new MessageEvent($message));

        return $message;
    }

    /**
     * Purges the queue
     */
    public function purge()
    {
        $this->eventDispatcher->dispatch(Events::PURGE);
        return $this->adapter->purge();
    }

    /**
     * Acknowledges message
     *
     * @param Message $message
     */
    public function ack(Message $message)
    {
        $this->eventDispatcher->dispatch(Events::ACK, new MessageEvent($message));
        return $this->adapter->ack($message->getIdentifier());
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @param EventSubscriberInterface $subscriber
     * @return Queue
     */
    public function addSubscriber(EventSubscriberInterface $subscriber)
    {
        $this->eventDispatcher->addSubscriber($subscriber);
        return $this;
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

}
