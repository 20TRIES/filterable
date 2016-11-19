<?php

namespace _20TRIES\Test\RequestToQueryAdaptor;

use _20TRIES\Filterable\RequestToQueryAdaptor;
use _20TRIES\Filterable\Param;
use _20TRIES\Test\TestingRequest;
use Closure;

class StringConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function test_that_string_config_a() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => []];
            public function scopes() {
                return [
                    'bar' => 'barScope'
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

    public function test_that_string_config_a_2() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 'foo'];
            public function scopes() {
                return ['bar' => 'barScope()'];
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

    public function test_that_string_config_b() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 25];
            public function scopes() {
                return ['bar' => 'barScope(bar)'];
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
            $this->assertAttributeEquals('bar', 'name', $configuration[1]);
        }
    }

    /**
     * @expectedException \_20TRIES\Filterable\Exceptions\InvalidConfigurationException
     * @expectedExceptionMessage Scope parameters must be included within the configuration key
     */
    public function test_that_string_config_c() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 25, 'foo' => 30];
            public function scopes() {
                return ['bar' => 'barScope(foo)'];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
        $this->assertInternalType('array', $configurations);
        $this->assertEmpty($configurations);
    }

    public function test_that_string_config_d() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 25, 'foo' => 30];
            public function scopes() {
                return ['foo,bar' => 'barScope(foo,bar)'];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 25, 'foo' => 30];
            public function scopes() {
                return ['foo,bar' => 'barScope(bar,foo)'];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 25, 'foo' => 30];
            public function scopes() {
                return [
                    'foo,bar' => 'barScope(bar,foo)',
                    'bar,foo' => 'barScope(bar,foo)',
                ];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
        $this->assertInternalType('array', $configurations);
        $this->assertEquals(count($configurations), 1);
    }

    public function test_that_string_config_g() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 25, 'foo' => 30];
            public function scopes() {
                return ['foo,bar' => 'barScope(25,bar)'];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 25, 'foo' => 30];
            public function scopes() {
                return ['foo,bar' => 'barScope(bar, 25)'];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 25, 'foo' => 30];
            public function scopes() {
                return ['foo,bar' => 'barScope(25.1,bar)'];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 25, 'foo' => 30, '25' => 35];
            public function scopes() {
                return ['foo,bar' => 'barScope(25,bar)'];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 25, 'foo' => 30];
            public function scopes() {
                return ['foo,bar' => 'barScope("25",bar)'];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 25, 'foo' => 30];
            public function scopes() {
                return ['foo,bar' => "barScope('hello',bar)"];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 25, 'foo' => 30, 'hello' => 35];
            public function scopes() {
                return ['foo,bar' => "barScope('hello',bar)"];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['bar' => 'baz'];
            public function scopes() {
                return [
                    'bar' => 'barScope(bar, 35)'
                ];
            }
        };
        $query = $this
            ->getMockBuilder('MockClass')
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->with('baz', 35)->willReturnSelf();
        $adaptor->adapt($request, $query);
    }

    public function test_that_string_config_n() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['foo' => 30];
            public function scopes() {
                return ['foo' => "barScope(true)"];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['foo' => 30];
            public function scopes() {
                return ['foo' => "barScope(false)"];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['foo' => 30, 'true' => 40];
            public function scopes() {
                return ['foo' => "barScope(false)"];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['foo' => ['bar' => 1, 'baz' => 2]];
            public function scopes() {
                return ['foo.bar' => "barScope(foo.bar)"];
            }
        };

        $configurations = $adaptor->getConfiguration($request);
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