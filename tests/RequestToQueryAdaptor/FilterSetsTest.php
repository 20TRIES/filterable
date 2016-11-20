<?php

namespace _20TRIES\Test\RequestToQueryAdaptor;

use _20TRIES\Filterable\RequestToQueryAdaptor;
use PHPUnit_Framework_TestCase;

class FilterSetsTest extends PHPUnit_Framework_TestCase
{
    public function test_that_configuration_matches_wildcard_when_no_patterns_match() {
        $input = [];
        $configuration = [
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
                ''           => 'customPaginate(1, 15)',
            ],
        ];
        $parsed_configuration = RequestToQueryAdaptor::parseConfiguration($configuration, $input);
        $this->assertInternalType('array', $parsed_configuration);
        $this->assertArrayHasKey(0, $parsed_configuration);
        $this->assertInternalType('array', $parsed_configuration[0]);
        $this->assertEquals(3, count($parsed_configuration[0]));
        $this->assertInstanceOf(\Closure::class, $parsed_configuration[0][0]);
        $query = $this->getMockBuilder('MockObject')->setMethods(['customPaginate'])->getMock();
        $query->expects($this->once())->method('customPaginate')->willReturnSelf();
        $closure = $parsed_configuration[0][0];
        $closure($query);
        $this->assertEquals(1, $parsed_configuration[0][1]);
        $this->assertEquals(15, $parsed_configuration[0][2]);
    }

    /**
     * @expectedException \_20TRIES\Filterable\Exceptions\InvalidConfigurationException
     * @expectedExceptionMessage Duplicated filter
     */
    public function test_that_configuration_sets_trigger_duplicate_exceptions_with_non_sets() {
        $input = [];
        $configuration = [
            'page,limit' => 'fooBar()',
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
            ],
        ];
        $query = $this->getMockBuilder('MockObject')->getMock();
        RequestToQueryAdaptor::adapt($configuration, $input, $query);
    }

    /**
     * @expectedException \_20TRIES\Filterable\Exceptions\InvalidConfigurationException
     * @expectedExceptionMessage Duplicated filter
     */
    public function test_that_configuration_sets_trigger_duplicate_exceptions_with_sets() {
        $input = [];
        $configuration = [
            [
                'page,limit' => 'customPaginate(page, limit)',
            ],
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
            ],
        ];
        $query = $this->getMockBuilder('MockObject')->getMock();
        RequestToQueryAdaptor::adapt($configuration, $input, $query);
    }

    // No exception should be thrown.
    public function test_that_set_wildcards_do_not_trigger_duplicated_exceptions() {
        $input = [];
        $configuration = [
            [
                'foo,bar' => 'fooBar(foo, bar)',
                ''        => 'fooBar()',
            ],
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
                ''           => 'customPaginate(1, 15)',
            ],
        ];
        $query = $this->getMockBuilder('MockObject')->setMethods(['customPaginate', 'fooBar'])->getMock();
        $query->expects($this->once())->method('customPaginate')->with(1, 15)->willReturnSelf();
        $query->expects($this->once())->method('fooBar')->with()->willReturnSelf();
        RequestToQueryAdaptor::adapt($configuration, $input, $query);
    }

    public function test_only_one_pattern_in_set_matches() {
        $input = ['page' => 2, 'limit' => 5];
        $configuration = [
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
                ''           => 'customPaginate(1, 15)',
            ],
        ];
        $query = $this->getMockBuilder('MockObject')->setMethods(['customPaginate'])->getMock();
        $query->expects($this->once())->method('customPaginate')->with(2, 5)->willReturnSelf();
        RequestToQueryAdaptor::adapt($configuration, $input, $query);
    }

    public function test_patterns_match_in_order() {
        $input = ['page' => 2, 'limit' => 5];
        $configuration = [
            [
                'page'       => 'customPaginate(page, 15)',
                'page,limit' => 'customPaginate(page, limit)',
                'limit'      => 'customPaginate(1, limit)',
                ''           => 'customPaginate(1, 15)',
            ],
        ];
        $query = $this->getMockBuilder('MockObject')->setMethods(['customPaginate'])->getMock();
        $query->expects($this->once())->method('customPaginate')->with(2, 15)->willReturnSelf();
        RequestToQueryAdaptor::adapt($configuration, $input, $query);
    }
}