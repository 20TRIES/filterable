<?php namespace _20TRIES\Test;

use _20TRIES\Filterable\Filterable;
use PHPUnit_Framework_TestCase;

class TraitTest extends PHPUnit_Framework_TestCase
{
    public function test_basic_use_of_filerable_trait()
    {
        $model = $this->getMockBuilder('MockModel')
            ->setMethods(['newQuery'])
            ->getMock();

        $query = $this->getMockBuilder('MockQuery')
            ->setMethods(['fooScope'])
            ->getMock();

        $model->expects($this->once())
            ->method('newQuery')
            ->willReturn($query);

        $query->expects($this->once())
            ->method('fooScope')
            ->willReturnSelf();

        $controller = new Controller($model);

        $controller->index(['foo' => 'bar']);
    }
}

class Controller {

    use Filterable;

    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    protected function filtering()
    {
        return [
            'foo' => 'fooScope()',
        ];
    }

    public function index($input = [])
    {
        $query = $this->applyFiltering($this->model->newQuery(), $input);
    }
}