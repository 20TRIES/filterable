<?php namespace _20TRIES\Test\Compiler;

use _20TRIES\Filterable\Compiler;
use _20TRIES\Filterable\Param;
use Closure;
use PHPUnit_Framework_TestCase;

class StringTest extends PHPUnit_Framework_TestCase
{
    public function test_that_string_config_a() {
        $config = [
            'bar' => 'barScope'
        ];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
            $this->assertEquals('bar', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(1, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);
        }
    }

    public function test_that_string_config_a_2() {
        $config = ['bar' => 'barScope()'];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
            $this->assertEquals('bar', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(1, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);
        }
    }

    public function test_that_string_config_b() {
        $config = ['bar' => 'barScope(bar)'];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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
        $config = ['bar' => 'barScope(foo)'];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertEmpty($compiled);
    }

    public function test_that_string_config_d() {
        $config = ['foo,bar' => 'barScope(foo,bar)'];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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
        $config = ['foo,bar' => 'barScope(bar,foo)'];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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
     * @expectedExceptionMessage Duplicated configuration item
     */
    public function test_that_string_config_f() {
        $config = [
            'foo,bar' => 'barScope(bar,foo)',
            'bar,foo' => 'barScope(bar,foo)',
        ];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertEquals(count($compiled), 1);
    }

    public function test_that_string_config_g() {
        $config = ['foo,bar' => 'barScope(25,bar)'];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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
        $config = ['foo,bar' => 'barScope(bar, 25)'];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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
        $config = ['foo,bar' => 'barScope(25.1,bar)'];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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
        $config = ['foo,bar' => 'barScope(25,bar)'];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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
        $config = ['foo,bar' => 'barScope("25",bar)'];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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
        $config = ['foo,bar' => "barScope('hello',bar)"];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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
        $config = ['foo,bar' => "barScope('hello',bar)"];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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

    public function test_that_string_config_n() {
        $config = ['foo' => "barScope(true)"];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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
        $config = ['foo' => "barScope(false)"];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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
        $config = ['foo' => "barScope(false)"];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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
        $config = ['foo.bar' => "barScope(foo.bar)"];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
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
        $query = new \stdClass;
        $compiled[0][0]($query, 1, 50);

        $this->assertInstanceOf(Param::class, $compiled[0][1]);
        $this->assertAttributeEquals('limit', 'name', $compiled[0][1]);

        $this->assertInstanceOf(Param::class, $compiled[0][2]);
        $this->assertAttributeEquals('page', 'name', $compiled[0][2]);
    }

    /**
     * @expectedException \_20TRIES\Filterable\Exceptions\InvalidConfigurationException
     * @expectedExceptionMessage Duplicated configuration item
     */
    public function test_that_configuration_sets_trigger_duplicate_exceptions_with_non_sets() {
        $config = [
            'page,limit' => 'fooBar()',
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
            ],
        ];
        (new Compiler)->compile($config);
    }

    /**
     * @expectedException \_20TRIES\Filterable\Exceptions\InvalidConfigurationException
     * @expectedExceptionMessage Duplicated configuration item
     */
    public function test_that_configuration_sets_trigger_duplicate_exceptions_with_non_sets_when_ordered() {
        $config = [
            'limit,page' => 'fooBar()',
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
            ],
        ];
        (new Compiler)->compile($config);
    }

    /**
     * @expectedException \_20TRIES\Filterable\Exceptions\InvalidConfigurationException
     * @expectedExceptionMessage Duplicated configuration item
     */
    public function test_that_configuration_sets_trigger_duplicate_exceptions_with_sets() {
        $config = [
            [
                'page,limit' => 'customPaginate(page, limit)',
            ],
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
            ],
        ];
        (new Compiler)->compile($config);
    }

    // No exception should be thrown.
    public function test_that_set_wildcards_do_not_trigger_duplicated_exceptions() {
        $config = [
            [
                'foo,bar' => 'fooBar(foo, bar)',
                ''        => 'fooBar()',
            ],
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
                ''           => 'customPaginate(1, 15)',
            ],
        ];
        (new Compiler)->compile($config);
    }
}