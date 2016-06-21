<?php

namespace _20TRIES\Test;

use _20TRIES\Filterable\Filterable;
use Illuminate\Database\Eloquent\Builder;

class LimitArgumentTest extends \PHPUnit_Framework_TestCase
{
    public function test_resolution_is_disabled_by_resolve_input_option()
    {
        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['resolveCollections', 'resolveFilters', 'resolveLoads', 'registerSharedVariables'])
            ->getMockForTrait();

        $mock->expects($this->never())->method('resolveCollections');
        $mock->expects($this->never())->method('resolveFilters');
        $mock->expects($this->never())->method('resolveLoads');

        $mock->initialiseFilters(['resolve_input' => false]);
    }

    public function test_pagination_is_applied_to_query_by_default()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $expected = 15;

        $mock->expects($this->any())->method('getInput')->willReturn([]);

        $mock_query
            ->expects($this->once())
            ->method('simplePaginate')
            ->with($this->equalTo($expected));

        $mock->initialiseFilters();
        $mock->buildQuery($mock_query);
    }

    public function test_is_overriden_by_limit_passed_passed_to_initilise_filters()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['registerSharedVariables', 'getInput'])
            ->getMockForTrait();

        $expected = 99;

        $mock->expects($this->any())->method('getInput')->willReturn([]);

        $mock_query
            ->expects($this->once())
            ->method('simplePaginate')
            ->with($this->equalTo($expected));

        $mock->initialiseFilters(['limit' => $expected]);
        $mock->buildQuery($mock_query);
    }

    public function test_default_lower_limit_boundary_is_enforced()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

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
            ->with($this->equalTo($expected));

        $mock->initialiseFilters(['limit' => $input]);
        $mock->buildQuery($mock_query);
    }

    public function test_default_upper_limit_boundary_is_enforced()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

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
            ->with($this->equalTo($expected));

        $mock->initialiseFilters(['limit' => $input]);
        $mock->buildQuery($mock_query);
    }

    public function test_default_lower_limit_is_customisable()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

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
            ->with($this->equalTo($expected));

        $mock->initialiseFilters(['limit' => $input, 'limit_max' => $input]);
        $mock->buildQuery($mock_query);
    }

    public function test_default_upper_limit_is_customisable()
    {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

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
            ->with($this->equalTo($expected));

        $mock->initialiseFilters(['limit' => $input, 'limit_min' => $input]);
        $mock->buildQuery($mock_query);
    }

    public function test_pagination_appends_current_input()
    {
        $mock_query = $this->getMock(Builder::class, ['simplePaginate', 'appends'], [], '', false);

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

        $mock_query
            ->expects($this->once())
            ->method('appends')
            ->with($expected);

        $mock->initialiseFilters([]);
        $mock->buildQuery($mock_query);
    }
}
