<?php

use _20TRIES\Filterable\Adaptors\FilteringAdaptor;
use _20TRIES\Filterable\Filters\BasicSelectFilter;
use \_20TRIES\Filterable\Adaptors\Interfaces\HasFilters;
use _20TRIES\Filterable\Filters\Filter;

class FilteringAdaptorTest extends PHPUnit_Framework_TestCase
{
    public function test_scope_config_with_string() {
        $adaptor = new FilteringAdaptor('foo');
        $request = new class() extends MockRequest {
            protected $input = ['foo' => ['bar' => []]];
            public function filters() {
                return ['bar' => 'barScope'];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->willReturnSelf();
        $adaptor->adapt($request, $query);
    }

    public function test_scope_config_with_closure() {
        $adaptor = new FilteringAdaptor('foo');
        $request = new class() extends MockRequest {
            protected $input = ['foo' => ['bar' => []]];
            public function filters() {
                return [
                    'bar' => function ($query) {
                        return $query->barScope();
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
        $adaptor->adapt($request, $query);
    }

    public function test_scope_config_with_filter_class() {
        $adaptor = new FilteringAdaptor('foo');
        $request = new class() extends MockRequest {
            protected $input = ['foo' => ['bar' => []]];
            public function filters() {
                return ['bar' => BasicSelectFilter::on('barScope'),];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->willReturnSelf();
        $adaptor->adapt($request, $query);
    }

    /**
     * @expectedException \_20TRIES\Filterable\Exceptions\InvalidConfigurationException
     */
    public function test_unresolvable_scope_method_causes_exception() {
        $adaptor = new FilteringAdaptor('foo');
        $request = new class() extends MockRequest {
            protected $input = ['foo' => ['bar' => []]];
            public function filters() {
                return ['bar' => 1];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adaptor->adapt($request, $query);
    }

    public function test_single_scope_attribute_is_passed_to_model_method() {
        $adaptor = new FilteringAdaptor('foo');
        $request = new class() extends MockRequest {
            protected $input = ['foo' => ['bar' => 'baz']];
            public function filters() {
                return ['bar' => 'barScope'];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->with('baz')->willReturnSelf();
        $adaptor->adapt($request, $query);
    }

    public function test_multiple_scope_attributes_are_passed_to_model_method() {
        $adaptor = new FilteringAdaptor('foo');
        $request = new class() extends MockRequest {
            protected $input = ['foo' => ['bar' => ['baz', 'bip']]];
            public function filters() {
                return ['bar' => 'barScope'];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->with('baz', 'bip')->willReturnSelf();
        $adaptor->adapt($request, $query);
    }

    public function test_scope_not_built_into_query() {
        $adaptor = new FilteringAdaptor('foo');
        $request = new class() extends MockRequest {
            protected $input = ['foo' => ['baz' => ['bip', 'bop']]];
            public function filters() {
                return ['bar' => 'barScope'];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['baz'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->never())->method('baz')->withAnyParameters();
        $adaptor->adapt($request, $query);
    }

    public function test_that_input_values_are_mutated() {
        $adaptor = new FilteringAdaptor('foo');
        $filter = $this
            ->getMockBuilder(Filter::class)
            ->setMethods(['getMutatedValues'])
            ->getMock();
        $filter->expects($this->once())->method('getMutatedValues')->willReturn(['foo', 'bar']);
        $filter->setMethod('barScope');
        $request = new class($filter) extends MockRequest {
            protected $input = ['foo' => ['bar' => []]];
            protected $filter;
            public function __construct($filter){
                $this->filter = $filter;
            }
            public function filters() {
                return ['bar' => $this->filter];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['barScope'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('barScope')->with('foo', 'bar')->willReturnSelf();
        $adaptor->adapt($request, $query);
    }

    public function test_default_parameter_name() {
        $this->assertEquals('filters', (new FilteringAdaptor())->getParameter());
    }
}

abstract class MockRequest implements HasFilters {
    protected $input = [];
    public function get($key, $default = null) {
        return array_key_exists($key, $this->input) ? $this->input[$key] : $default;
    }
}