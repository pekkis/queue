<?php

/**
 * This file is part of the pekkis-queue package.
 *
 * For copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pekkis\Queue\Filter;

use IteratorAggregate;
use Closure;
use ArrayIterator;

class OutputFilters implements IteratorAggregate
{
    /**
     * @var array
     */
    protected $filters = array();

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->filters);
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function add(Closure $callable)
    {
        $this->filters[] = $callable;
        return $this;
    }

    /**
     * @param $str
     * @return string
     */
    public function filter($str)
    {
        foreach ($this as $filter) {
            $str = $filter($str);
        }

        return $str;
    }
}
