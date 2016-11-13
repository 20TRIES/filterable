<?php

namespace _20TRIES\Test\RequestToQueryAdaptor;

use _20TRIES\Filterable\Adaptors\RequestToQueryAdaptor;
use _20TRIES\Filterable\Param;
use _20TRIES\Test\TestingRequest;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class ArrayConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function test_that_array_config_a() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 45];
            public function scopes() {
                return [
                    'bar' => function ($query) {

                    },
                ];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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

    public function test_that_array_config_b() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 45];
            public function scopes() {
                return [
                    'bar' => [function ($query) {

                    }],
                ];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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

    public function test_that_array_config_c() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 45];
            public function scopes() {
                return [
                    'bar' => [
                        function ($query, $bar) {

                        },
                        new Param('bar')
                    ],
                ];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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
            $this->assertEquals('bar', $configuration[1]->name());
        }
    }

    public function test_that_array_config_d() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 45];
            public function scopes() {
                return [
                    'bar' => [
                        function ($query, $bar) {

                        },
                        20
                    ],
                ];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 45];
            public function scopes() {
                return [
                    'bar' => [
                        function ($query, $bar) {

                        },
                        new Param('bar'),
                        20,
                    ],
                ];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 45];
            public function scopes() {
                return [
                    'bar' => [
                        function ($query, $bar) {

                        },
                        20,
                        new Param('bar'),
                    ],
                ];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 45];
            public function scopes() {
                return [
                    'bar' => [
                        function ($query, $bar) {

                        },
                        'mock_param'
                    ],
                ];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
        $this->assertInternalType('array', $configurations);
        $this->assertNotEmpty($configurations);

        foreach ($configurations as $filter => $configuration) {
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

    public function test_scope_attributes_are_passed_to_model_method() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 'baz'];
            public function scopes() {
                return [
                    'bar' => [function($query, ...$args) {
                        return $query->barScope(...$args);
                    }, new Param('bar'), 35]
                ];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->with('baz', 35)->willReturnSelf();
        $adaptor->adapt($request, $query);
    }
}