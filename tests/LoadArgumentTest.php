<?php

namespace _20TRIES\Test;

use _20TRIES\Filterable\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;

/**
 * A test quite for testing handling of the load attribute which can be passed to filterable in a request.
 *
 * @since v0
 */
class LoadArgumentTest extends \PHPUnit_Framework_TestCase
{
    public function test_load_argument_is_correctly_interpreted()
    {
        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['getInput', 'registerSharedVariables'])
            ->getMockForTrait();

        $mock->expects($this->any())->method('getInput')->willReturn([
            'load' => ['relationOne', 'relationTwo', 'relationThree'],
        ]);

        $mock->initialiseFilters();

        $this->assertEquals(['relationOne', 'relationTwo', 'relationThree'], $mock->shouldLoad());
    }

    public function test_loads_are_passed_to_builder()
    {
        $mock_query = $this->getMock(Builder::class, ['simplePaginate', 'with'], [], '', false);

        $mock_paginator = $this->getMock(Paginator::class, ['appends', 'toArray'], [], '', false);

        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['getInput', 'registerSharedVariables'])
            ->getMockForTrait();

        $mock->expects($this->any())->method('getInput')->willReturn([
            'load' => ['relationOne', 'relationTwo', 'relationThree'],
        ]);

        $mock_query->expects($this->any())->method('simplePaginate')->willReturn($mock_paginator);
        $mock_paginator->expects($this->any())->method('appends')->willReturnSelf();
        $mock_paginator->expects($this->any())->method('toArray');

        $mock_query
            ->expects($this->once())
            ->method('with')
            ->with($this->equalTo(['relationOne', 'relationTwo', 'relationThree']))
            ->willReturnSelf();

        $mock->initialiseFilters();
        $mock->buildQuery($mock_query);
    }
}
