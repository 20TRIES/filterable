<?php

namespace _20TRIES\Test;

use _20TRIES\Filterable\Adaptor;
use _20TRIES\Filterable\Compiler;
use _20TRIES\Filterable\Param;
use PHPUnit_Framework_TestCase;

class AdaptorTest extends PHPUnit_Framework_TestCase
{
    public function test_scope_attributes_are_passed_to_model_method_using_array_config() {
        $query = $this->getMockBuilder('MockClass')->setMethods(['barScope'])->getMock();
        $query->expects($this->once())->method('barScope')->with('baz', 35)->willReturnSelf();
        $closure = function($query, ...$args) {
            return $query->barScope(...$args);
        };
        $config = [
            'bar' => [$closure, new Param('bar'), 35]
        ];
        $input = ['bar' => 'baz'];
        $compiled = (new Compiler())->compile($config);
        (new Adaptor())->adapt($compiled, $input, $query);
    }

    public function test_scope_attributes_are_passed_to_model_method_using_string_config() {
        $query = $this
            ->getMockBuilder('MockClass')
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->with('baz', 35)->willReturnSelf();
        $config = [
            'bar' => 'barScope(bar, 35)'
        ];
        $input = ['bar' => 'baz'];
        $compiled = (new Compiler())->compile($config);
        (new Adaptor())->adapt($compiled, $input, $query);
    }

    // FILTER SETS

    public function test_that_configuration_matches_wildcard_when_no_patterns_match() {
        $input = [];
        $config = [
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
                ''           => 'customPaginate(1, 15)',
            ],
        ];
        $compiled = (new Compiler())->compile($config);
        $query = $this->getMockBuilder('MockObject')->setMethods(['customPaginate'])->getMock();
        $query->expects($this->once())->method('customPaginate')->with(1, 15)->willReturnSelf();
        (new Adaptor)->adapt($compiled, $input, $query);
    }

    /**
     * @expectedException \_20TRIES\Filterable\Exceptions\InvalidConfigurationException
     * @expectedExceptionMessage Duplicated filter
     */
    public function test_that_configuration_sets_trigger_duplicate_exceptions_with_non_sets() {
        $input = [];
        $config = [
            'page,limit' => 'fooBar()',
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
            ],
        ];
        $compiled = (new Compiler())->compile($config);
        $query = $this->getMockBuilder('MockObject')->getMock();
        (new Adaptor)->adapt($compiled, $input, $query);
    }

    /**
     * @expectedException \_20TRIES\Filterable\Exceptions\InvalidConfigurationException
     * @expectedExceptionMessage Duplicated filter
     */
    public function test_that_configuration_sets_trigger_duplicate_exceptions_with_sets() {
        $input = [];
        $config = [
            [
                'page,limit' => 'customPaginate(page, limit)',
            ],
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
            ],
        ];
        $compiled = (new Compiler())->compile($config);
        $query = $this->getMockBuilder('MockObject')->getMock();
        (new Adaptor)->adapt($compiled, $input, $query);
    }

    // No exception should be thrown.
    public function test_that_set_wildcards_do_not_trigger_duplicated_exceptions() {
        $input = [];
        $config = [
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
        $compiled = (new Compiler())->compile($config);
        $query = $this->getMockBuilder('MockObject')->setMethods(['customPaginate', 'fooBar'])->getMock();
        $query->expects($this->once())->method('customPaginate')->with(1, 15)->willReturnSelf();
        $query->expects($this->once())->method('fooBar')->with()->willReturnSelf();
        (new Adaptor)->adapt($compiled, $input, $query);
    }

    public function test_only_one_pattern_in_set_matches() {
        $input = ['page' => 2, 'limit' => 5];
        $config = [
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
                ''           => 'customPaginate(1, 15)',
            ],
        ];
        $compiled = (new Compiler())->compile($config);
        $query = $this->getMockBuilder('MockObject')->setMethods(['customPaginate'])->getMock();
        $query->expects($this->once())->method('customPaginate')->with(2, 5)->willReturnSelf();
        (new Adaptor)->adapt($compiled, $input, $query);
    }

    public function test_patterns_match_in_order() {
        $input = ['page' => 2, 'limit' => 5];
        $config = [
            [
                'page'       => 'customPaginate(page, 15)',
                'page,limit' => 'customPaginate(page, limit)',
                'limit'      => 'customPaginate(1, limit)',
                ''           => 'customPaginate(1, 15)',
            ],
        ];
        $compiled = (new Compiler())->compile($config);
        $query = $this->getMockBuilder('MockObject')->setMethods(['customPaginate'])->getMock();
        $query->expects($this->once())->method('customPaginate')->with(2, 15)->willReturnSelf();
        (new Adaptor)->adapt($compiled, $input, $query);
    }
}