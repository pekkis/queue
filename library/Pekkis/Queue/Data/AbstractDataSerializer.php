<?php

namespace Pekkis\Queue\Data;

abstract class AbstractDataSerializer implements DataSerializer
{
    /**
     * If the serializer is not parametrized then class name is enough
     *
     * @return string
     */
    public function getIdentifier()
    {
        return get_class($this);
    }
}
