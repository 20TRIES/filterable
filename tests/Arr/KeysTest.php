<?php namespace _20TRIES\Test\Arr;

use _20TRIES\Filterable\Arr;
use PHPUnit_Framework_TestCase;

class KeysTest extends PHPUnit_Framework_TestCase
{
    public function test_single_dimension_with_integer_keys()
    {
        $result = Arr::keys(['foo', 'bar', 'baz']);
        $this->assertContains(0, $result);
        $this->assertContains(1, $result);
        $this->assertContains(2, $result);
    }

    // @TODO Leaf keys test
//    public function test_multi_dimension_with_integer_keys()
//    {
//        $results = Arr::keys([0 => [1 => [2 => [4 => 5], 3 => 4]]]);
//
//        $this->assertCount(5, $results);
//
//        $this->assertArrayHasKey(0, $results);
//        $this->assertEquals('0', $results[0]);
//
//        $this->assertArrayHasKey(1, $results);
//        $this->assertEquals('0.1', $results[1]);
//
//        $this->assertArrayHasKey(2, $results);
//        $this->assertEquals('0.1.2', $results[2]);
//
//        $this->assertArrayHasKey(3, $results);
//        $this->assertEquals('0.1.2.4', $results[3]);
//
//        $this->assertArrayHasKey(4, $results);
//        $this->assertEquals('0.1.3', $results[4]);
//    }

    public function test_keys_on_empty_array()
    {
        $this->assertEmpty(Arr::keys([]));
    }

    public function test_keys_on_simple_string_keyed_array()
    {
        $result = Arr::keys(['foo' => 1, 'bar' => 2, 'baz' => 3]);
        $this->assertContains('foo', $result);
        $this->assertContains('bar', $result);
        $this->assertContains('baz', $result);
    }

    public function test_keys_on_simple_int_keyed_array()
    {
        $result = Arr::keys(['foo', 'bar', 'baz']);
        $this->assertContains(0, $result);
        $this->assertContains(1, $result);
        $this->assertContains(2, $result);
    }

    public function test_keys_on_simple_mixed_keyed_array()
    {
        $result = Arr::keys(['foo', 'bar' => 22, 'baz']);
        $this->assertContains(0, $result);
        $this->assertContains(1, $result);
        $this->assertContains('bar', $result);
    }

    public function test_keys_on_nested_string_keyed_array()
    {
        $result = Arr::keys(['foo' => ['bar' => 1], 'baz' => 2]);
        $this->assertContains('foo.bar', $result);
        $this->assertContains('baz', $result);
    }

    public function test_keys_on_nested_int_keyed_array()
    {
        $result = Arr::keys([[1], 2]);
        $this->assertContains('0.0', $result);
        $this->assertContains('1', $result);
    }

    public function test_keys_on_nested_mixed_keyed_array()
    {
        $result = Arr::keys(['foo' => [1], 2]);
        $this->assertContains('foo.0', $result);
        $this->assertContains(0, $result);
    }

    public function test_keys_when_subsets_of_the_same_key_are_used()
    {
        $this->assertTrue(Arr::has([1 => [2 => 1, 3 => 2]], ['1.2', '1']));
    }
}