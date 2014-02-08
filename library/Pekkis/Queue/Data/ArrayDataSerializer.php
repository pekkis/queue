<?php

namespace Pekkis\Queue\Data;

class ArrayDataSerializer extends AbstractDataSerializer implements DataSerializer
{
    public function willSerialize($unserialized)
    {
        if (is_array($unserialized)) {
            return true;
        }

        return false;
    }

    public function serialize($unserialized)
    {
        return json_encode($unserialized);
    }

    public function unserialize($serialized)
    {
        return json_decode($serialized, true);
    }

}
