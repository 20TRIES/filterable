<?php

namespace _20TRIES\Test\RequestToQueryAdaptor;

use _20TRIES\Filterable\Compiler;
use _20TRIES\Filterable\Param;
use PHPUnit_Framework_TestCase;
use Closure;

class CompilerTest extends PHPUnit_Framework_TestCase
{
    public function test_that_configuration_set_compiles_to_optimal() {
        $configurations = [
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
                ''           => 'customPaginate(1, 15)',
            ]
        ];
        $adaptor = $this->getMockBuilder('foo')->setMethods(['adaptSet'])->getMock();
        $adaptor->expects($this->once())->method('adaptSet')->with();
        $compiler = new Compiler($adaptor);
        $compiled = $compiler->compile($configurations);

        $this->assertInternalType('array', $compiled);

        $this->assertEquals(1, count($compiled));

        $this->assertEquals(3, count($compiled[0]));

        $this->assertInstanceOf(Closure::class, $compiled[0][0]);
        $query = new \stdClass();
        $compiled[0][0]($query, 1, 50);

        $this->assertInstanceOf(Param::class, $compiled[0][1]);
        $this->assertAttributeEquals('page', 'name', $compiled[0][1]);

        $this->assertInstanceOf(Param::class, $compiled[0][2]);
        $this->assertAttributeEquals('limit', 'name', $compiled[0][2]);
    }
}