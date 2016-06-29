<?php

namespace _20TRIES\Test;

use _20TRIES\Filterable\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;

class LimitArgumentTest extends \PHPUnit_Framework_TestCase
{
    public function test_resolution_is_disabled_by_resolve_input_option()
    {
        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['resolveCollections', 'resolveFilters', 'resolveLoads', 'registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $mock->expects($this->any())->method('getInput')->willReturn([]);
        $mock->expects($this->never())->method('resolveCollections');
        $mock->expects($this->never())->method('resolveFilters');
        $mock->expects($this->never())->method('resolveLoads');

        $mock->initialiseFilters(['resolve_input' => false]);
    }

    public function test_pagination_is_applied_to_query_by_default()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, [], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $expected = 15;

        $mock->expects($this->any())->method('getInput')->willReturn([]);

        $mock_query
            ->expects($this->once())
            ->method('simplePaginate')
            ->with($this->equalTo($expected))
            ->willReturn($mock_paginator);

        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();

        $mock->initialiseFilters();
        $mock->buildQuery($mock_query);
    }

    public function test_limit_is_resolved_from_input_if_no_limit_option_is_passed()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, [], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $expected = 30;

        $mock->expects($this->any())->method('getInput')->willReturn(['limit' => $expected]);

        $mock_query
            ->expects($this->once())
            ->method('simplePaginate')
            ->with($this->equalTo($expected))
            ->willReturn($mock_paginator);

        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();

        $mock->initialiseFilters();
        $mock->buildQuery($mock_query);
    }

    public function test_limit_option_overrides_input()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, [], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $expected = 30;

        $mock->expects($this->any())->method('getInput')->willReturn(['limit' => 99]);

        $mock_query
            ->expects($this->once())
            ->method('simplePaginate')
            ->with($this->equalTo($expected))
            ->willReturn($mock_paginator);

        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();

        $mock->initialiseFilters(['limit' => $expected]);
        $mock->buildQuery($mock_query);
    }

    public function test_default_limit_is_overridden_by_limit_option_passed_to_initilise_filters()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, [], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $expected = 99;

        $mock->expects($this->any())->method('getInput')->willReturn([]);

        $mock_query
            ->expects($this->once())
            ->method('simplePaginate')
            ->with($this->equalTo($expected))
            ->willReturn($mock_paginator);

        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();

        $mock->initialiseFilters(['limit' => $expected]);
        $mock->buildQuery($mock_query);
    }

    public function test_default_lower_limit_boundary_is_enforced()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, [], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $input = -100;
        $expected = 0;

        $mock->expects($this->any())->method('getInput')->willReturn([]);

        $mock_query
            ->expects($this->once())
            ->method('simplePaginate')
            ->with($this->equalTo($expected))
            ->willReturn($mock_paginator);

        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();

        $mock->initialiseFilters(['limit' => $input]);
        $mock->buildQuery($mock_query);
    }

    public function test_default_upper_limit_boundary_is_enforced()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, [], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $input = 1000;
        $expected = 100;

        $mock->expects($this->any())->method('getInput')->willReturn([]);

        $mock_query
            ->expects($this->once())
            ->method('simplePaginate')
            ->with($this->equalTo($expected))
            ->willReturn($mock_paginator);

        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();

        $mock->initialiseFilters(['limit' => $input]);
        $mock->buildQuery($mock_query);
    }

    public function test_default_lower_limit_is_customisable()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, [], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $input = 1000;
        $expected = 1000;

        $mock->expects($this->any())->method('getInput')->willReturn([]);

        $mock_query
            ->expects($this->once())
            ->method('simplePaginate')
            ->with($this->equalTo($expected))
            ->willReturn($mock_paginator);

        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();

        $mock->initialiseFilters(['limit' => $input, 'limit_max' => $input]);
        $mock->buildQuery($mock_query);
    }

    public function test_default_upper_limit_is_customisable()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, [], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $input = -100;
        $expected = -100;

        $mock->expects($this->any())->method('getInput')->willReturn([]);

        $mock_query
            ->expects($this->once())
            ->method('simplePaginate')
            ->with($this->equalTo($expected))
            ->willReturn($mock_paginator);

        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();

        $mock->initialiseFilters(['limit' => $input, 'limit_min' => $input]);
        $mock->buildQuery($mock_query);
    }

    public function test_pagination_appends_current_input()
    {
        $mock_query = $this->getMock(Builder::class, ['simplePaginate'], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, ['appends', 'toArray'], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $mock_input = [
            'a'    => 1,
            'b'    => 2,
            'c'    => 3,
            'page' => 5,
        ];

        $expected = [
            'a' => 1,
            'b' => 2,
            'c' => 3,
        ];

        $mock->expects($this->any())->method('getInput')->willReturn($mock_input);

        $mock_query->expects($this->any())->method('simplePaginate')->willReturn($mock_paginator);

        $mock_paginator->expects($this->once())->method('appends')->with($expected)->willReturnSelf();

        $mock_paginator->expects($this->any())->method('toArray');

        $mock->initialiseFilters([]);
        $mock->buildQuery($mock_query);
    }

    public function test_pagination_not_disableable_by_default()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, [], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $mock->expects($this->any())->method('getInput')->willReturn(['limit' => -1]);

        $mock_query->expects($this->once())->method('simplePaginate')->willReturn($mock_paginator);

        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();

        $mock->initialiseFilters([]);
        $mock->buildQuery($mock_query);
    }

    public function test_pagination_is_disabled_by_passing_negative_value_when_disabling_is_configured()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, ['simplePaginate'], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $mock->expects($this->any())->method('getInput')->willReturn(['limit' => -1]);

        $mock_query->expects($this->once())->method('get')->willReturnSelf();

        $mock_paginator->expects($this->never())->method('simplePaginate');

        $mock->initialiseFilters(['limit_can_disable' => true]);
        $mock->buildQuery($mock_query);
    }

    public function test_pagination_is_not_disabled_by_default_when_disabling_is_configured()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, ['appends', 'toArray'], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $mock->expects($this->any())->method('getInput')->willReturn([]);

        $mock_query->expects($this->once())->method('simplePaginate')->willReturn($mock_paginator);

        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();
        $mock_paginator->expects($this->any())->method('toArray');

        $mock->initialiseFilters(['limit_can_disable' => true]);
        $mock->buildQuery($mock_query);
    }

    public function test_pagination_to_array_is_returned_when_pagination_is_active()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, ['appends', 'toArray'], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $mock->expects($this->any())->method('getInput')->willReturn([]);

        $mock_query->expects($this->once())->method('simplePaginate')->willReturn($mock_paginator);

        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();

        $mock_result = 'some mock result';

        $mock_paginator->expects($this->once())->method('toArray')->willReturn($mock_result);

        $mock->initialiseFilters([]);

        $result = $mock->buildQuery($mock_query);

        $this->assertEquals($mock_result, $result);
    }

    public function test_builder_to_array_is_returned_wrapped_in_array_containing_data_attribute_when_pagination_is_inactive()
    {
        $mock_query = $this->getMock(Builder::class, ['get', 'toArray'], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, ['simplePaginate'], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $mock->expects($this->any())->method('getInput')->willReturn(['limit' => -1]);

        $mock_query->expects($this->once())->method('get')->willReturnSelf();

        $mock_result_data = 'some_mock_result_data';

        $mock_query->expects($this->once())->method('toArray')->willReturn($mock_result_data);

        $mock_paginator->expects($this->never())->method('simplePaginate');

        $mock->initialiseFilters(['limit_can_disable' => true]);

        $result = $mock->buildQuery($mock_query);

        $this->assertEquals(['data' => $mock_result_data], $result);
    }
}
