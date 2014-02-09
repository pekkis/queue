<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Data;

class NullDataSerializer extends AbstractDataSerializer implements DataSerializer
{
    public function willSerialize($unserialized)
    {
        if (is_null($unserialized)) {
            return true;
        }

        return false;
    }

    public function serialize($unserialized)
    {
        return json_encode(null);
    }

    public function unserialize($serialized)
    {
        return null;
    }

}
