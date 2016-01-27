<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Data;

class DataSerializers
{
    private $serializers = array();

    /**
     * @param DataSerializer $serializer
     */
    public function add(DataSerializer $serializer)
    {
        $this->serializers[] = $serializer;
    }

    /**
     * @return DataSerializer[]
     */
    public function getSerializers()
    {
        return $this->serializers;
    }

    /**
     * @param string $identifier
     * @return DataSerializer
     */
    protected function getSerializerByIdentifier($identifier)
    {
        foreach ($this->getSerializers() as $serializer) {
            if ($serializer->getIdentifier() === $identifier) {
                return $serializer;
            }
        }
        return false;
    }

    /**
     * @param mixed $unserialized
     * @return DataSerializer
     */
    public function getSerializerFor($unserialized)
    {
        foreach (array_reverse($this->getSerializers()) as $serializer) {
            if ($serializer->willSerialize($unserialized)) {
                return $serializer;
            }
        }
        return false;
    }

    /**
     * @param SerializedData $data
     * @return DataSerializer
     */
    public function getUnserializerFor(SerializedData $data)
    {
        return $this->getSerializerByIdentifier($data->getSerializerIdentifier());
    }
}
