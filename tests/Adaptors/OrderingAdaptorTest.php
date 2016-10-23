<?php

use \_20TRIES\Filterable\Adaptors\Interfaces\HasFilters;
use _20TRIES\Filterable\Adaptors\Interfaces\HasOrderings;
use _20TRIES\Filterable\Adaptors\OrderingAdaptor;

class OrderingAdaptorTest extends PHPUnit_Framework_TestCase
{
    public function test_ordering_config_with_array() {
        $adaptor = new OrderingAdaptor('foo');
        $request = new class() extends OrderingMockRequest {
            protected $input = ['foo' => 'highest-rated'];
            public function filters() {
                return [];
            }
            public function orderings() {
                return [
                    'highest-rated' => ['orderBy', 'rating', 'desc'],
                ];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['orderBy'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('orderBy')->with('rating', 'desc')->willReturnSelf();
        $adaptor->adapt($request, $query);
    }

    public function test_ordering_config_with_closure() {
        $adaptor = new OrderingAdaptor('foo');
        $request = new class() extends OrderingMockRequest {
            protected $input = ['foo' => 'highest-rated'];
            public function filters() {
                return [];
            }
            public function orderings() {
                return [
                    'highest-rated' => function ($query) {
                        return $query->orderBy('rating', 'desc');
                    },
                ];
            }
        };
        $query = $this
            ->getMockBuilder(\Illuminate\Database\Eloquent\Builder::class)
            ->setMethods(['orderBy'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('orderBy')->with('rating', 'desc')->willReturnSelf();
        $adaptor->adapt($request, $query);
    }

    public function test_default_parameter_name() {
        $this->assertEquals('order', (new OrderingAdaptor())->getParameter());
    }
}

abstract class OrderingMockRequest implements HasFilters, HasOrderings  {
    protected $input = [];
    public function get($key, $default = null) {
        return array_key_exists($key, $this->input) ? $this->input[$key] : $default;
    }
}