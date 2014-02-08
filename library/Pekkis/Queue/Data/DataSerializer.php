<?php

namespace Pekkis\Queue\Data;

interface DataSerializer
{
    /**
     * @param $unserialized
     * @return bool
     */
    public function willSerialize($unserialized);

    /**
     * @param $unserialized
     * @return SerializedData
     */
    public function serialize($unserialized);

    /**
     * @param string $serialized
     * @return mixed
     */
    public function unserialize($serialized);

    /**
     * @return string
     */
    public function getIdentifier();
}
