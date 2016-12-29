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