<?php

namespace _20TRIES\Test;


use _20TRIES\Filterable\Param;
use _20TRIES\Filterable\ParamSet;
use PHPUnit_Framework_TestCase;

class ParameterSetTest extends PHPUnit_Framework_TestCase
{
    public function test_construction()
    {
        $set = new ParamSet('foo', 'bar', 'baz');
        $this->assertAttributeInternalType('array', 'parameters', $set);
        $this->assertAttributeCount(3, 'parameters', $set);
        $this->assertAttributeEquals([
            new Param('foo'),
            new Param('bar'),
            new Param('baz'),
        ], 'parameters', $set);
    }

    public function test_getter()
    {
        $set = new ParamSet('foo', 'bar', 'baz');
        $this->assertEquals([
            new Param('foo'),
            new Param('bar'),
            new Param('baz'),
        ], $set->parameters());
    }
}