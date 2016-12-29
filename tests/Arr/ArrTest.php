<?php namespace _20TRIES\Test\Arr;

use _20TRIES\Filterable\Arr;
use PHPUnit_Framework_TestCase;

class ArrTest extends PHPUnit_Framework_TestCase
{
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

    // @TODO test_reduce

    // @TODO test_filter

    // @TODO test_first

    // @TODO test_head (test simple example as this just aliases the first method)

    // @TODO test_tail (test simple example as this just aliases the array_slice method)
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}