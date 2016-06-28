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
    public function test_load_argument_is_correctly_interpreted() {
        $controller = new TestControllerOne();
        $controller->index();
        $this->assertEquals(['relationOne', 'relationTwo', 'relationThree'], $controller->shouldLoad());
    }

    public function test_loads_are_passed_to_builder() {
        $mock_query = $this->getMock(Builder::class, [], [], '', false);

        $controller = new TestControllerTwo();

        $mock_query
            ->expects($this->once())
            ->method('with')
            ->with($this->equalTo(['relationOne', 'relationTwo', 'relationThree']));

        $controller->index($mock_query);
    }
}

class TestControllerOne {
    use Filterable;

    protected $func;

    public function index() {
        $this->initialiseFilters();
    }

    public function getInput() {
        return  ['load' => ['relationOne', 'relationTwo', 'relationThree']];
    }

    public function registerSharedVariables() {
    }
};

class TestControllerTwo {

    use Filterable;

    protected $func;

    public function index($query)
    {
        $this->initialiseFilters();
        $this->buildQuery($query);
    }

    public function getInput()
    {
        return  ['load' => ['relationOne', 'relationTwo', 'relationThree']];
    }

    public function registerSharedVariables()
    {
    }
};