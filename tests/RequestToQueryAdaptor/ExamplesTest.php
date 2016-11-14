<?php

namespace _20TRIES\Test\RequestToQueryAdaptor;

use _20TRIES\Filterable\RequestToQueryAdaptor;
use _20TRIES\Filterable\Param;
use _20TRIES\Test\TestingRequest;
use Illuminate\Database\Eloquent\Builder;

class ExamplesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group examples
     */
    public function test_example_ordering() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['order' => 'highest-rated'];
            public function scopes() {
                return [
                    'order' => [function($query, $ordering) {
                        switch($ordering) {
                            case "highest-rated":
                                return $query->orderBy('rating', 'desc');
                                break;
                            default:
                                // Throw Exception
                        };
                        return $query;
                    }, new Param('order')],
                ];
            }
        };
        $query = $this
            ->getMockBuilder(Builder::class)
            ->setMethods(['orderBy'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('orderBy')->with('rating', 'desc')->willReturnSelf();
        $adaptor->adapt($request, $query);
    }

    /**
     * @group examples
     */
    public function test_example_pagination() {
        $adaptor = new RequestToQueryAdaptor();
        $request = new class() extends TestingRequest {
            protected $input = ['page' => 10, 'limit' => 50];
            public function scopes() {
                return [
                    'page,limit' => 'customPaginate(page, limit)',
                ];
            }
        };
        $query = $this
            ->getMockBuilder(Builder::class)
            ->setMethods(['customPaginate'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('customPaginate')->with(10, 50)->willReturnSelf();
        $adaptor->adapt($request, $query);
    }
}