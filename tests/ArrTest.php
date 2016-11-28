<?php namespace _20TRIES\Test;

use _20TRIES\Filterable\Arr;
use PHPUnit_Framework_TestCase;

class ArrTest extends PHPUnit_Framework_TestCase
{
    public function test_get_returns_false_when_key_is_empty() {
        $result = Arr::get([], '');
        $this->assertEquals(null, $result);
    }

    public function test_get_returns_null_when_key_is_empty_and_arr_not_empty() {
        $result = Arr::get(['foo' => 1, 'bar' => 2], '');
        $this->assertEquals(null, $result);
    }

    public function test_get_returns_default() {
        $result = Arr::get([], '', 'mock-foo');
        $this->assertEquals('mock-foo', $result);
    }

    public function test_get_matches_on_string() {
        $result = Arr::get(['foo' => 1, 'bar' => 2], 'foo');
        $this->assertEquals(1, $result);
    }

    public function test_get_matches_on_int() {
        $result = Arr::get([1, 'bar' => 2], 0);
        $this->assertEquals(1, $result);
    }

    public function test_get_matches_on_nested_string() {
        $result = Arr::get([1, 'bar' => ['foo' => 2]], 'bar.foo');
        $this->assertEquals(2, $result);
    }

    public function test_get_matches_on_mixed() {
        $result = Arr::get([['foo' => 1], 'bar' => 2], '0.foo');
        $this->assertEquals(1, $result);
    }

    public function test_get_matches_nested_int() {
        $result = Arr::get([[2], 'bar' => 2], "0.0");
        $this->assertEquals(2, $result);
    }

    public function test_get_finds_deeply_nested_elements() {
        $result = Arr::get([1, 'foo' => ['bar' => ['baz' => 3], 2]], 'foo.bar.baz');
        $this->assertEquals(3, $result);
    }

    public function test_get_returns_null_when_no_match() {
        $result = Arr::get([1, 'foo' => ['bar' => ['baz', 2]]], 'foo.bar.baz');
        $this->assertEquals(null, $result);
    }

    public function test_has_returns_false_when_key_is_empty() {
        $result = Arr::has(['foo' => false], 'foo');
        $this->assertTrue($result);
    }

    public function test_only_with_string_key() {
        $input = [1, 'foo' => ['bar' => ['baz', 2]]];
        $this->assertEquals(['foo' => ['bar' => ['baz', 2]]], Arr::only($input, ['foo']));
    }

    public function test_only_with_int_key() {
        $input = [1, 'foo' => ['bar' => ['baz', 2]]];
        $this->assertEquals([1], Arr::only($input, [0]));
    }

    public function test_only_with_multiple_keys() {
        $input = [1, 'foo' => ['bar' => ['baz', 2]]];
        $this->assertEquals([1, 'foo' => ['bar' => ['baz', 2]]], Arr::only($input, ['foo', 0]));
    }
}