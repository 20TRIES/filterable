<?php

namespace _20TRIES\Test\RequestToQueryAdaptor;

use _20TRIES\Filterable\Adaptor;
use _20TRIES\Filterable\Param;
use PHPUnit_Framework_TestCase;

class ExamplesTest extends PHPUnit_Framework_TestCase
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
        (new Adaptor())->adapt([
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
        (new Adaptor())->adapt([
            'page,limit' => 'customPaginate(page, limit)',
        ], ['page' => 10, 'limit' => 50], $query);
    }

    /**
     * @group examples
     */
    public function test_example_pagination_with_sets_configured() {
        $query = $this
            ->getMockBuilder('MockClass')
            ->setMethods(['customPaginate'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('customPaginate')->with(10, 15)->willReturnSelf();
        (new Adaptor())->adapt([
            [
                'page,limit' => 'customPaginate(page, limit)',
                'page'       => 'customPaginate(page, 15)',
                'limit'      => 'customPaginate(1, limit)',
                ''           => 'customPaginate(1, 15)',
            ]
        ], ['page' => 10], $query);
    }

    /**
     * @group examples
     */
    public function test_example_fixed_limit_pagination_with_sets_configured() {
        $query = $this
            ->getMockBuilder('MockClass')
            ->setMethods(['customPaginate'])
            ->disableOriginalConstructor()
            ->getMock();
        $query->expects($this->once())->method('customPaginate')->with(10, 15)->willReturnSelf();
        (new Adaptor())->adapt([
            [
                'page' => 'customPaginate(page, 15)',
                ''     => 'customPaginate(1, 15)',
            ]
        ], ['page' => 10, 'limit' => 999999999], $query);
    }
}