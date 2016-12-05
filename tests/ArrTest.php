<?php namespace _20TRIES\Test;

use _20TRIES\Filterable\Arr;
use PHPUnit_Framework_TestCase;

class ArrTest extends PHPUnit_Framework_TestCase
{
    public function test_has_returns_false_when_key_is_empty_and_array_is_empty()
    {
        $this->assertFalse(Arr::has([], ''));
    }

    public function test_has_returns_false_when_key_is_empty_and_arr_not_empty()
    {
        $this->assertFalse(Arr::has(['foo' => 1, 'bar' => 2], ''));
    }

    public function test_has_matches_on_string()
    {
        $match = null;
        $result = Arr::has(['foo' => 1, 'bar' => 2], 'foo', $match);
        $this->assertTrue($result);
        $this->assertNotNull($match);
        $this->assertEquals(1, $match);
    }

    public function test_has_matches_on_int()
    {
        $match = null;
        $result = Arr::has([1, 'bar' => 2], 0, $match);
        $this->assertTrue($result);
        $this->assertNotNull($match);
        $this->assertEquals(1, $match);
    }

    public function test_has_leaves_value_unchanged_when_not_matched()
    {
        $match = 'foo-bar-baz-mock-value';
        $result = Arr::has([1, 'bar' => 2], 10, $match);
        $this->assertFalse($result);
        $this->assertEquals('foo-bar-baz-mock-value', $match);
    }

    public function test_has_matches_on_nested_string()
    {
        $match = null;
        $result = Arr::has([1, 'bar' => ['foo' => 2]], 'bar.foo', $match);
        $this->assertTrue($result);
        $this->assertNotNull($match);
        $this->assertEquals(2, $match);
    }

    public function test_has_matches_on_mixed()
    {
        $match = null;
        $result = Arr::has([['foo' => 1], 'bar' => 2], '0.foo', $match);
        $this->assertTrue($result);
        $this->assertNotNull($match);
        $this->assertEquals(1, $match);
    }

    public function test_has_matches_nested_int()
    {
        $match = null;
        $result = Arr::has([[2], 'bar' => 2], '0.0', $match);
        $this->assertTrue($result);
        $this->assertNotNull($match);
        $this->assertEquals(2, $match);
    }

    public function test_has_finds_deeply_nested_elements()
    {
        $match = null;
        $result = Arr::has([1, 'foo' => ['bar' => ['baz' => 3], 2]], 'foo.bar.baz', $match);
        $this->assertTrue($result);
        $this->assertNotNull($match);
        $this->assertEquals(3, $match);
    }

    public function test_has_returns_boolean_and_not_element_matched()
    {
        $this->assertTrue(Arr::has(['foo' => false], 'foo'));
    }

    public function test_get_returns_null_when_no_match_and_no_default_provided()
    {
        $this->assertNull(null, Arr::get([1, 'foo' => ['bar' => ['baz', 2]]], 'foo.bar.baz'));
    }

    public function test_get_returns_default()
    {
        $this->assertEquals('mock-foo', Arr::get([], '', 'mock-foo'));
    }

    public function test_only_with_string_key()
    {
        $input = [1, 'foo' => ['bar' => ['baz', 2]]];
        $this->assertEquals(['foo' => ['bar' => ['baz', 2]]], Arr::only($input, ['foo']));
    }

    public function test_only_with_int_key()
    {
        $input = [1, 'foo' => ['bar' => ['baz', 2]]];
        $this->assertEquals([1], Arr::only($input, [0]));
    }

    public function test_only_with_multiple_keys()
    {
        $input = [1, 'foo' => ['bar' => ['baz', 2]]];
        $this->assertEquals([1, 'foo' => ['bar' => ['baz', 2]]], Arr::only($input, ['foo', 0]));
    }
}