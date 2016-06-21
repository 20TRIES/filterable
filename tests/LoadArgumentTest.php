<?php

namespace _20TRIES\Test;

use _20TRIES\Filterable\Filterable;
use Illuminate\Database\Eloquent\Builder;

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
        $mock = $this
            ->getMockBuilder(Filterable::class)
            ->setMethods(['getInput', 'registerSharedVariables'])
            ->getMockForTrait();

        $mock->expects($this->any())->method('getInput')->willReturn([
            'load' => ['relationOne', 'relationTwo', 'relationThree'],
        ]);

        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $mock_query
            ->expects($this->once())
            ->method('with')
            ->with($this->equalTo(['relationOne', 'relationTwo', 'relationThree']));

        $mock->initialiseFilters();
        $mock->buildQuery($mock_query);
    }
}