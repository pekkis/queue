<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Filter;

use ArrayIterator;

class InputFilters extends OutputFilters
{
    public function getIterator()
    {
        return new ArrayIterator(array_reverse($this->filters));
    }
}
