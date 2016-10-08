<?php

use \_20TRIES\Filterable\Adaptors\ScopedAdaptor;

class ScopedAdaptorTest extends PHPUnit_Framework_TestCase
{
    public function test_scope_config_with_string() {
        $adaptor = new ScopedAdaptor('foo');
        $input =['foo' => ['bar' => []]];
        $request = new class($input) extends \Symfony\Component\HttpFoundation\Request {
            public function scopes() {
                return $scopes = [
                    'bar' => 'barScope'
                ];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->willReturnSelf();
        $adaptor->handle($request, $query);
    }

    public function test_scope_config_with_closure() {
        $adaptor = new ScopedAdaptor('foo');
        $input = ['foo' => ['bar' => []]];
        $request = new class($input) extends \Symfony\Component\HttpFoundation\Request {
            public function scopes() {
                return $scopes = [
                    'bar' => function ($query, $params) {
                        return $query->barScope($params);
                    }
                ];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->willReturnSelf();
        $adaptor->handle($request, $query);
    }

    public function test_scope_config_with_array_containing_method_string() {
        $adaptor = new ScopedAdaptor('foo');
        $input = ['foo' => ['bar' => []]];
        $request = new class($input) extends \Symfony\Component\HttpFoundation\Request {
            public function scopes() {
                return $scopes = [
                    'bar' => ['method' => 'barScope']
                ];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->willReturnSelf();
        $adaptor->handle($request, $query);
    }

    public function test_scope_config_with_array_containing_method_closure() {
        $adaptor = new ScopedAdaptor('foo');
        $input = ['foo' => ['bar' => []]];
        $request = new class($input) extends \Symfony\Component\HttpFoundation\Request {
            public function scopes() {
                return $scopes = [
                    'bar' => [
                        'method' => function ($query, $params) {
                            return $query->barScope($params);
                        }
                    ],
                ];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->willReturnSelf();
        $adaptor->handle($request, $query);
    }

    /**
     * @expectedException \_20TRIES\Filterable\Exceptions\InvalidConfigurationException
     */
    public function test_unresolvable_scope_method_causes_exception() {
        $adaptor = new ScopedAdaptor('foo');
        $input = ['foo' => ['bar' => []]];
        $request = new class($input) extends \Symfony\Component\HttpFoundation\Request {
            public function scopes() {
                return $scopes = [
                    'bar' => 1
                ];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adaptor->handle($request, $query);
    }

    public function test_single_scope_attribute_is_passed_to_model_method() {
        $adaptor = new ScopedAdaptor('foo');
        $input = ['foo' => ['bar' => 'baz']];
        $request = new class($input) extends \Symfony\Component\HttpFoundation\Request {
            public function scopes() {
                return $scopes = [
                    'bar' => 'barScope'
                ];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->with('baz')->willReturnSelf();
        $adaptor->handle($request, $query);
    }

    public function test_multiple_scope_attributes_are_passed_to_model_method() {
        $adaptor = new ScopedAdaptor('foo');
        $input = ['foo' => ['bar' => ['baz', 'bip']]];
        $request = new class($input) extends \Symfony\Component\HttpFoundation\Request {
            public function scopes() {
                return $scopes = [
                    'bar' => 'barScope'
                ];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->with(['baz', 'bip'])->willReturnSelf();
        $adaptor->handle($request, $query);
    }

    public function test_scope_not_built_into_query() {
        $adaptor = new ScopedAdaptor('foo');
        $input = ['foo' => ['baz' => ['bip', 'bop']]];
        $request = new class($input) extends \Symfony\Component\HttpFoundation\Request {
            public function scopes() {
                return $scopes = [
                    'bar' => 'barScope'
                ];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['baz'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->never())->method('baz')->withAnyParameters();
        $adaptor->handle($request, $query);
    }
}