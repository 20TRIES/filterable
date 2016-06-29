<?php

namespace _20TRIES\Test;

use _20TRIES\Filterable\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;

class OrderingTest extends \PHPUnit_Framework_TestCase
{
    public function test_default_ordering_configuration()
    {
        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $mock->expects($this->any())->method('getInput')->willReturn([]);

        $mock->initialiseFilters();

        $this->assertEquals(null, $mock->ordersBy());
        $this->assertEquals('asc', $mock->ordersByDirection());
    }

    public function test_ordering_is_configured_from_options()
    {
        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $mock->expects($this->any())->method('getInput')->willReturn([]);

        $mock_attr_name = 'mock_attr_name';

        $mock_order_dir = 'desc';

        $mock->initialiseFilters(['order' => [$mock_attr_name, $mock_order_dir]]);
        
        $this->assertEquals($mock_attr_name, $mock->ordersBy());
        $this->assertEquals($mock_order_dir, $mock->ordersByDirection());
    }

    public function test_ordering_is_resolved_from_input()
    {
        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $mock_attr_name = 'mock_attr_name';

        $mock_order_dir = 'desc';

        $mock->expects($this->any())->method('getInput')->willReturn(['order' => [$mock_attr_name, $mock_order_dir]]);

        $mock->initialiseFilters();

        $this->assertEquals($mock_attr_name, $mock->ordersBy());
        $this->assertEquals($mock_order_dir, $mock->ordersByDirection());
    }

    public function test_ordering_configuration_overrides_ordering_resolved_from_input()
    {
        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $mock_attr_name = 'mock_attr_name';

        $mock_order_dir = 'desc';

        $mock_attr_name_2 = 'mock_attr_name';

        $mock_order_dir_2 = 'desc';

        $mock->expects($this->any())->method('getInput')->willReturn(['order' => [$mock_attr_name, $mock_order_dir]]);

        $mock->initialiseFilters(['order' => [$mock_attr_name_2, $mock_order_dir_2]]);

        $this->assertEquals($mock_attr_name_2, $mock->ordersBy());
        $this->assertEquals($mock_order_dir_2, $mock->ordersByDirection());
    }

    public function test_ordering_is_not_resolved_from_input_when_resolved_input_disabled()
    {
        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $mock_attr_name = 'mock_attr_name';

        $mock_order_dir = 'desc';

        $mock->expects($this->any())->method('getInput')->willReturn(['order' => [$mock_attr_name, $mock_order_dir]]);

        $mock->initialiseFilters(['resolve_input' => false]);

        $this->assertNotEquals($mock_attr_name, $mock->ordersBy());
        $this->assertNotEquals($mock_order_dir, $mock->ordersByDirection());
    }

    public function test_invalid_order_direction_is_not_taken_from_input()
    {
        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $mock_attr_name = 'mock_attr_name';

        $mock_order_dir = 'mock_order_dir';

        $mock->expects($this->any())->method('getInput')->willReturn(['order' => [$mock_attr_name, $mock_order_dir]]);

        $mock->initialiseFilters();


        $this->assertEquals($mock_attr_name, $mock->ordersBy());
        $this->assertNotEquals($mock_order_dir, $mock->ordersByDirection());
    }

    public function test_ordering_is_passed_to_query_builder_if_order_attribute_has_been_set()
    {
        $mock_query = $this->getMock(Builder::class, ['simplePaginate', 'orderBy'], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, ['appends', 'toArray'], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['getInput', 'registerSharedVariables'])
            ->getMockForTrait();

        $mock_attr_name = 'mock_attr_name';

        $mock_order_dir = 'desc';

        $mock->expects($this->any())->method('getInput')->willReturn(['order' => [$mock_attr_name, $mock_order_dir]]);

        $mock_query->expects($this->any())->method('simplePaginate')->willReturn($mock_paginator);
        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();

        $mock_query
            ->expects($this->once())
            ->method('orderBy')
            ->with($mock_attr_name, $mock_order_dir)
            ->willReturnSelf();

        $mock->initialiseFilters();
        $mock->buildQuery($mock_query);
    }

    public function test_ordering_is_not_passed_to_query_builder_if_no_orderings_are_input()
    {
        $mock_query = $this->getMock(Builder::class, ['simplePaginate', 'orderBy'], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, ['appends', 'toArray'], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['getInput', 'registerSharedVariables'])
            ->getMockForTrait();

        $mock->expects($this->any())->method('getInput')->willReturn([]);

        $mock_query->expects($this->any())->method('simplePaginate')->willReturn($mock_paginator);
        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();

        $mock_query->expects($this->never())->method('orderBy');

        $mock->initialiseFilters();
        $mock->buildQuery($mock_query);
    }
}