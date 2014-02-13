<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    /**
     * @return string
     */
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
     * @return string
     */
    public function toJson()
    {
        return json_encode(
            array(
                'serializerIdentifier' => $this->serializerIdentifier,
                'data' => $this->data,
            )
        );
    }

    /**
     * @param string $json
     * @return SerializedData
     */
    public static function fromJson($json)
    {
        $decoded = json_decode($json, true);
        return new static(
            $decoded['serializerIdentifier'],
            $decoded['data']
        );
    }
}
