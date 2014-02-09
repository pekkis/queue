<?php

namespace Pekkis\Queue\Tests\Filter;

use Pekkis\Queue\Filter\OutputFilters;
use Pekkis\Queue\Tests\TestCase;

class OutputFiltersTest extends TestCase
{
    /**
     * @test
     */
    public function sameResultWithNoFilters()
    {
        $filters = new OutputFilters();
        $this->assertEquals('lusso grande', $filters->filter('lusso grande'));
    }

    /**
     * @test
     */
    public function filtersThroughAllFilters()
    {
        $filter1 = function ($str) {
            return str_rot13($str);
        };

        $filter2 = function ($str) {
            return $str . 'lussutusta';
        };

        $filters = new OutputFilters();
        $filters
            ->add($filter1)
            ->add($filter2);

        $ret = $filters->filter('putinin suuruuden ylistyksen kisat 2014');

        $expected = str_rot13('putinin suuruuden ylistyksen kisat 2014') . 'lussutusta';

        $this->assertEquals($expected, $ret);
    }
}
