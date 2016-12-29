<?php namespace _20TRIES\Test\Arr;

use _20TRIES\Filterable\Arr;
use PHPUnit_Framework_TestCase;

class HasTest extends PHPUnit_Framework_TestCase
{
    public function test_has_does_not_trim_keys()
    {
        $this->assertTrue(Arr::has([' ' => 1], ' '));
        $this->assertFalse(Arr::has(['' => 1], ' '));
        $this->assertTrue(Arr::has([' ' => ['  ' => 1]], ' .  '));
    }

    public function test_has_handles_non_array_sub_element()
    {
        $this->assertFalse(Arr::has(['a' => 1], 'a.b'));
    }

    public function test_has_with_single_dot()
    {
        $this->assertTrue(Arr::has(['' => ['' => 1]], '.'));
        $this->assertFalse(Arr::has(['' => 1], '.'));
    }

    public function test_has_with_empty_string()
    {
        $this->assertTrue(Arr::has(['' => 1], ''));
        $this->assertFalse(Arr::has(['0' => 1], ''));
        $this->assertFalse(Arr::has(['false' => 1], ''));
        $this->assertFalse(Arr::has([0 => 1], ''));
        $this->assertFalse(Arr::has([false => 1], ''));
    }

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

    public function test_has_on_string()
    {
        $this->assertTrue(Arr::has(['foo' => 1], ['foo']));
    }

    public function test_has_when_subsets_of_the_same_key_are_used()
    {
        $this->assertTrue(Arr::has([1 => [2 => 1]], ['1.2', '1']));
    }

    /**
     * Not duplicate, tested separately then single key test because the implementation of both is independent.
     */
    public function test_has_on_multiple_nested_integers()
    {
        $this->assertTrue(Arr::has([1 => [2 => [3 => [4 => [5 => 1]]]]], ['1.2.3.4.5', '1.2']));
    }

    /**
     * Not duplicate, tested separately then single key test because the implementation of both is independent.
     */
    public function test_has_on_multiple_nested_strings()
    {
        $this->assertTrue(Arr::has(
            ['foo' => ['bar' => 1], 'baz' => 2],
            ['foo.bar', 'baz']
        ));
    }

    /**
     * Not duplicate, tested separately then single key test because the implementation of both is independent.
     */
    public function test_has_on_deeply_nested_int()
    {
        $this->assertTrue(Arr::has(
            [[1, [2]], 3],
            ['0.1.0', '1']
        ));
    }

    /**
     * Not duplicate, tested separately then single key test because the implementation of both is independent.
     */
    public function test_has_on_deeply_nested_string()
    {
        $this->assertTrue(Arr::has(
            ['foo' => ['bar' => 1, 'baz' => ['bip' => 2]], 'bop' => 3],
            ['foo.baz.bip', 'bop']
        ));
    }

    public function tests_duplicated_keys_match()
    {
        $this->assertTrue(Arr::has(
            ['foo' => ['bar' => 1, 'baz' => ['bip' => 2]], 'bop' => 3],
            ['foo', 'foo.bar', 'foo.baz', 'foo.baz.bip', 'bop']
        ));
    }

    public function test_has_with_empty_array()
    {
        $this->assertFalse(Arr::has([], ['foo', 'bar', 'baz']));
    }

    public function test_has_with_extra_data_in_arr()
    {
        $this->assertTrue(Arr::has(['foo' => 1,'bar' => 2, 'baz' => 3], ['foo', 'bar']));
    }
}