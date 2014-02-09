<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
