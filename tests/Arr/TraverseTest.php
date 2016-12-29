<?php namespace _20TRIES\Test\Arr;

use _20TRIES\Filterable\Arr;
use PHPUnit_Framework_TestCase;

class TraverseTest extends PHPUnit_Framework_TestCase
{
    public function test_traversal_of_empty_array()
    {
        $input = [];
        $items = [];
        $callback = function ($item, $key) use (&$items) {
            $items[] = $key;
        };
        Arr::traverse($input, $callback);
        $this->assertCount(0, $items);
    }

    public function test_traversal_keys_values_and_order()
    {
        $input = ['1' => ['1' => 'foo', '2' => 'bar'], '2' => 'baz'];
        $items = [];
        $callback = function ($value, $key) use (&$items) {
            $items[] = [$key, $value];
        };
        Arr::traverse($input, $callback);

        $this->assertCount(4, $items);

        $this->assertArrayHasKey(0, $items);
        $this->assertInternalType('array', $items[0]);
        $this->assertCount(2, $items[0]);
        $this->assertEquals('1', $items[0][0]);
        $this->assertEquals(['1' => 'foo', '2' => 'bar'], $items[0][1]);

        $this->assertArrayHasKey(1, $items);
        $this->assertInternalType('array', $items[1]);
        $this->assertCount(2, $items[1]);
        $this->assertEquals('1.1', $items[1][0]);
        $this->assertEquals('foo', $items[1][1]);

        $this->assertArrayHasKey(2, $items);
        $this->assertInternalType('array', $items[2]);
        $this->assertCount(2, $items[2]);
        $this->assertEquals('1.2', $items[2][0]);
        $this->assertEquals('bar', $items[2][1]);

        $this->assertArrayHasKey(3, $items);
        $this->assertInternalType('array', $items[3]);
        $this->assertCount(2, $items[3]);
        $this->assertEquals('2', $items[3][0]);
        $this->assertEquals('baz', $items[3][1]);
    }

    public function test_items_after_sub_array_are_traversed()
    {
        $input = ['1' => 'baz', '2' => ['1' => 'foo', '2' => 'bar']];
        $items = [];
        $callback = function ($item, $key) use (&$items) {
            $items[] = $key;
        };
        Arr::traverse($input, $callback);
        $this->assertCount(4, $items);
    }
}