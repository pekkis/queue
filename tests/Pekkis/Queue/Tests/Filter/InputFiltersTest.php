<?php

namespace Pekkis\Queue\Tests\Filter;

use Pekkis\Queue\Filter\InputFilters;
use Pekkis\Queue\Tests\TestCase;

class InputFiltersTest extends TestCase
{
    /**
     * @test
     */
    public function sameResultWithNoFilters()
    {
        $filters = new InputFilters();
        $this->assertEquals('lusso grande', $filters->filter('lusso grande'));
    }

    /**
     * @test
     */
    public function reverselyFiltersThroughAllFilters()
    {
        $input = 'chgvava fhhehhqra lyvfglxfra xvfng 2014lussutusta';

        $filter1 = function ($str) {
            return str_rot13($str);
        };

        $filter2 = function ($str) {
            return substr($str, 0, strlen($str) - 10);
        };

        $filters = new InputFilters();
        $filters
            ->add($filter1)
            ->add($filter2);

        $ret = $filters->filter($input);

        $expected = 'putinin suuruuden ylistyksen kisat 2014';
        $this->assertEquals($expected, $ret);
    }
}
