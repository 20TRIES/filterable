<?php namespace _20TRIES\Test\Compiler;

use _20TRIES\Filterable\Compiler;
use _20TRIES\Filterable\Param;
use Closure;
use PHPUnit_Framework_TestCase;

class ArrayTest extends PHPUnit_Framework_TestCase
{
    public function test_that_array_config_a() {
        $config = [
            'bar' => function ($query) {

            },
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

    public function test_that_array_config_b() {
        $config = [
            'bar' => [function ($query) {

            }],
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

    public function test_that_array_config_c() {
        $config = [
            'bar' => [
                function ($query, $bar) {

                },
                new Param('bar')
            ],
        ];
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
            $this->assertEquals('bar', $configuration[1]->name());
        }
    }

    public function test_that_array_config_d() {
        $config = [
            'bar' => [
                function ($query, $bar) {

                },
                20
            ],
        ];
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
            $this->assertInternalType('int', $configuration[1]);
            $this->assertEquals(20, $configuration[1]);
        }
    }

    public function test_that_array_config_e() {
        $config = [
            'bar' => [
                function ($query, $bar) {

                },
                new Param('bar'),
                20,
            ],
        ];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
            $this->assertEquals('bar', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(3, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[1]);
            $this->assertEquals('bar', $configuration[1]->name());

            $this->assertArrayHasKey(2, $configuration);
            $this->assertInternalType('int', $configuration[2]);
            $this->assertEquals(20, $configuration[2]);
        }
    }

    public function test_that_array_config_f() {
        $config = [
            'bar' => [
                function ($query, $bar) {

                },
                20,
                new Param('bar'),
            ],
        ];
        $compiled = (new Compiler)->compile($config);
        $this->assertInternalType('array', $compiled);
        $this->assertNotEmpty($compiled);
        foreach ($compiled as $filter => $configuration) {
            $this->assertEquals('bar', $filter);
            $this->assertInternalType('array', $configuration);
            $this->assertEquals(3, count($configuration));

            $this->assertArrayHasKey(0, $configuration);
            $this->assertInstanceOf(Closure::class, $configuration[0]);

            $this->assertArrayHasKey(1, $configuration);
            $this->assertInternalType('int', $configuration[1]);
            $this->assertEquals(20, $configuration[1]);

            $this->assertArrayHasKey(2, $configuration);
            $this->assertInstanceOf(Param::class, $configuration[2]);
            $this->assertEquals('bar', $configuration[2]->name());
        }
    }

    public function test_that_array_config_g() {
        $config = [
            'bar' => [
                function ($query, $bar) {

                },
                'mock_param'
            ],
        ];
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
            $this->assertInternalType('string', $configuration[1]);
            $this->assertEquals('mock_param', $configuration[1]);
        }
    }
}