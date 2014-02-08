<?php

namespace Pekkis\Queue\Data;

class SerializedData
{
    /**
     * @var string
     */
    private $serializerIdentifier;

    /**
     * @var string
     */
    private $data;

    /**
     * @param string $serializerIdentifier
     * @param string $data
     */
    public function __construct($serializerIdentifier, $data)
    {
        $this->serializerIdentifier = $serializerIdentifier;
        $this->data = $data;
    }

    public function getSerializerIdentifier()
    {
        return $this->serializerIdentifier;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function toJson()
    {
        return json_encode(
            array(
                'serializerIdentifier' => $this->serializerIdentifier,
                'data' => $this->data,
            )
        );
    }

    public static function fromJson($json)
    {
        $decoded = json_decode($json, true);
        return new static(
            $decoded['serializerIdentifier'],
            $decoded['data']
        );
    }
}

