<?php

namespace _20TRIES\Test\RequestToQueryAdaptor;

use _20TRIES\Filterable\Adaptor;
use _20TRIES\Filterable\Param;
use Closure;
use PHPUnit_Framework_TestCase;

class StringConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function test_that_string_config_a() {
        $configurations = (new Adaptor())->parseConfiguration([
            'bar' => 'barScope'
        ], ['bar' => []]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('bar', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(1, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);
        }
    }

    public function test_that_string_config_a_2() {
        $configurations = (new Adaptor())->parseConfiguration(['bar' => 'barScope()'], ['bar' => 'foo']);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('bar', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(1, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);
        }
    }

    public function test_that_string_config_b() {
        $configurations = (new Adaptor())->parseConfiguration(['bar' => 'barScope(bar)'], ['bar' => 25]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('bar', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(2, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[1]);
            $this->assertAttributeEquals('bar', 'name', $configuration[1]);
        }
    }

    /**
     * @expectedException \_20TRIES\Filterable\Exceptions\InvalidConfigurationException
     * @expectedExceptionMessage Scope parameters must be included within the configuration key
     */
    public function test_that_string_config_c() {
        $configurations = (new Adaptor())->parseConfiguration(['bar' => 'barScope(foo)'], ['bar' => 25, 'foo' => 30]);
        $this->assertInternalType('array', $configurations);
        $this->assertEmpty($configurations);
    }

    public function test_that_string_config_d() {
        $configurations = (new Adaptor())->parseConfiguration(['foo,bar' => 'barScope(foo,bar)'], ['bar' => 25, 'foo' => 30]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('bar,foo', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(3, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[1]);
            $this->assertAttributeEquals('foo', 'name', $configuration[1]);

            $this->assertArrayHasKey(2, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[2]);
            $this->assertAttributeEquals('bar', 'name', $configuration[2]);
        }
    }

    public function test_that_string_config_e() {
        $configurations = (new Adaptor())->parseConfiguration(['foo,bar' => 'barScope(bar,foo)'], ['bar' => 25, 'foo' => 30]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('bar,foo', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(3, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[1]);
            $this->assertAttributeEquals('bar', 'name', $configuration[1]);

            $this->assertArrayHasKey(2, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[2]);
            $this->assertAttributeEquals('foo', 'name', $configuration[2]);
        }
    }

    /**
     * @expectedException \_20TRIES\Filterable\Exceptions\InvalidConfigurationException
     * @expectedExceptionMessage Duplicated filter
     */
    public function test_that_string_config_f() {
        $configurations = (new Adaptor())->parseConfiguration([
            'foo,bar' => 'barScope(bar,foo)',
            'bar,foo' => 'barScope(bar,foo)',
        ], ['bar' => 25, 'foo' => 30]);
        $this->assertInternalType('array', $configurations);
        $this->assertEquals(count($configurations), 1);
    }

    public function test_that_string_config_g() {
        $configurations = (new Adaptor())->parseConfiguration(['foo,bar' => 'barScope(25,bar)'], ['bar' => 25, 'foo' => 30]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('bar,foo', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(3, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInternalType('int', $configuration[1]);
            $this->assertEquals(25, $configuration[1]);

            $this->assertArrayHasKey(2, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[2]);
            $this->assertAttributeEquals('bar', 'name', $configuration[2]);
        }
    }

    public function test_that_string_config_h() {
        $configurations = (new Adaptor())->parseConfiguration(['foo,bar' => 'barScope(bar, 25)'], ['bar' => 25, 'foo' => 30]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('bar,foo', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(3, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[1]);
            $this->assertAttributeEquals('bar', 'name', $configuration[1]);
            $this->assertArrayHasKey(2, $configuration);
            $this->assertInternalType('int', $configuration[2]);
            $this->assertEquals(25, $configuration[2]);
        }
    }

    public function test_that_string_config_i() {
        $configurations = (new Adaptor())->parseConfiguration(['foo,bar' => 'barScope(25.1,bar)'], ['bar' => 25, 'foo' => 30]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('bar,foo', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(3, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInternalType('float', $configuration[1]);
            $this->assertEquals(25.1, $configuration[1]);

            $this->assertArrayHasKey(2, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[2]);
            $this->assertAttributeEquals('bar', 'name', $configuration[2]);
        }
    }

    public function test_that_string_config_j() {
        $configurations = (new Adaptor())->parseConfiguration(['foo,bar' => 'barScope(25,bar)'], ['bar' => 25, 'foo' => 30, '25' => 35]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('bar,foo', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(3, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInternalType('int', $configuration[1]);
            $this->assertEquals(25, $configuration[1]);

            $this->assertArrayHasKey(2, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[2]);
            $this->assertAttributeEquals('bar', 'name', $configuration[2]);
        }
    }

    public function test_that_string_config_k() {
        $configurations = (new Adaptor())->parseConfiguration(['foo,bar' => 'barScope("25",bar)'], ['bar' => 25, 'foo' => 30]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('bar,foo', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(3, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInternalType('string', $configuration[1]);
            $this->assertEquals("25", $configuration[1]);

            $this->assertArrayHasKey(2, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[2]);
            $this->assertAttributeEquals('bar', 'name', $configuration[2]);
        }
    }

    public function test_that_string_config_l() {
        $configurations = (new Adaptor())->parseConfiguration(['foo,bar' => "barScope('hello',bar)"], ['bar' => 25, 'foo' => 30]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('bar,foo', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(3, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInternalType('string', $configuration[1]);
            $this->assertEquals("hello", $configuration[1]);

            $this->assertArrayHasKey(2, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[2]);
            $this->assertAttributeEquals('bar', 'name', $configuration[2]);
        }
    }

    public function test_that_string_config_m() {
        $configurations = (new Adaptor())->parseConfiguration(['foo,bar' => "barScope('hello',bar)"], ['bar' => 25, 'foo' => 30, 'hello' => 35]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('bar,foo', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(3, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInternalType('string', $configuration[1]);
            $this->assertEquals("hello", $configuration[1]);

            $this->assertArrayHasKey(2, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[2]);
            $this->assertAttributeEquals('bar', 'name', $configuration[2]);
        }
    }

    public function test_scope_attributes_are_passed_to_model_method() {
        $query = $this
            ->getMockBuilder('MockClass')
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->with('baz', 35)->willReturnSelf();
        (new Adaptor())->adapt([
            'bar' => 'barScope(bar, 35)'
        ], ['bar' => 'baz'], $query);
    }

    public function test_that_string_config_n() {
        $configurations = (new Adaptor())->parseConfiguration(['foo' => "barScope(true)"], ['foo' => 30]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('foo', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(2, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInternalType('bool', $configuration[1]);
            $this->assertEquals(true, $configuration[1]);
        }
    }

    public function test_that_string_config_o() {
        $configurations = (new Adaptor())->parseConfiguration(['foo' => "barScope(false)"], ['foo' => 30]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('foo', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(2, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInternalType('bool', $configuration[1]);
            $this->assertEquals(false, $configuration[1]);
        }
    }

    public function test_that_string_config_p() {
        $configurations = (new Adaptor())->parseConfiguration(['foo' => "barScope(false)"], ['foo' => 30, 'true' => 40]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('foo', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(2, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInternalType('bool', $configuration[1]);
            $this->assertEquals(false, $configuration[1]);
        }
    }

    public function test_that_string_config_q() {
        $configurations = (new Adaptor())->parseConfiguration(['foo.bar' => "barScope(foo.bar)"], ['foo' => ['bar' => 1, 'baz' => 2]]);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);
        foreach ($configurations as $filter => $configuration) {
            $this->assertEquals('foo.bar', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(2, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[1]);
            $this->assertAttributeEquals('foo.bar', 'name', $configuration[1]);
        }
    }
}