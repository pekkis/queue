<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
