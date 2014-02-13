<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Data;

use stdClass;
use Serializable;

class BasicDataSerializer extends AbstractDataSerializer implements DataSerializer
{
    public function willSerialize($unserialized)
    {
        if (is_string($unserialized)
            || is_array($unserialized)
            || is_null($unserialized)
            || $unserialized instanceof stdClass
            || $unserialized instanceof Serializable
        ) {
            return true;
        }
        return false;
    }

    public function serialize($unserialized)
    {
        return serialize($unserialized);
    }

    public function unserialize($serialized)
    {
        return unserialize($serialized);
    }
}
