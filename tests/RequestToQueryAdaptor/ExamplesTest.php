<?php

namespace _20TRIES\Test\RequestToQueryAdaptor;

use _20TRIES\Filterable\RequestToQueryAdaptor;
use _20TRIES\Filterable\Param;

class ExamplesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group examples
     */
    public function test_example_ordering() {
        $query = $this
            ->getMockBuilder('MockClass')
            ->setMethods(['orderBy'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('orderBy')->with('rating', 'desc')->willReturnSelf();
        RequestToQueryAdaptor::adapt([
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
        ], ['order' => 'highest-rated'], $query);
    }

    /**
     * @group examples
     */
    public function test_example_pagination() {
        $query = $this
            ->getMockBuilder('MockClass')
            ->setMethods(['customPaginate'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('customPaginate')->with(10, 50)->willReturnSelf();
        RequestToQueryAdaptor::adapt([
            'page,limit' => 'customPaginate(page, limit)',
        ], ['page' => 10, 'limit' => 50], $query);
    }
}