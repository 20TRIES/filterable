<?php namespace _20TRIES\Filterable;

/**
 * Helper class for interacting with arrays in a simple and clean way.
 *
 * Supports "dot notation".
 *
 * @package _20TRIES\Filterable
 */
class Arr
{
    /**
     * Gets a parameter from a request.
     *
     * @param array $arr
     * @param string $key
     * @param mixed $default
     * @return null
     */
    public static function get($arr, $key, $default = null)
    {
        self::has($arr, $key, $default);
        return $default;
    }

    /**
     * Determines whether an array has a given key.
     *
     * @param array $arr
     * @param string|int $key
     * @param mixed $value
     * @return bool
     */
    public static function has($arr, $key, &$value = null)
    {
        foreach ($components = self::splitKey($key) as $component) {
            if (! array_key_exists($component, $arr)) {
                return false;
            }
            $arr = $arr[$component];
        }
        if (! empty($components)) {
            $value = $arr;
            return true;
        }
        return false;
    }

    /**
     * Splits a key into its components for "dot notation".
     *
     * @param string|int $key
     * @return array
     */
    protected static function splitKey($key)
    {
        return is_string($key) ? explode('.', trim($key)) : [$key];
    }

    /**
     * Gets a set of attributes from an array.
     *
     * The is method does not support "dot notation".
     *
     * @param array $arr
     * @param array $keys
     * @return array
     */
    public static function only($arr, $keys)
    {
        return array_intersect_key($arr, array_flip($keys));
    }

    /**
     * Gets the keys available in a "dot notation" array.
     *
     * @param array $arr
     * @return array
     */
    public static function keys($arr)
    {
        $keys = [];
        $sub_arrs = [['append' => '', 'data' => $arr]];
        while(! empty($sub_arrs)) {
            $sub_arr = array_pop($sub_arrs);
            foreach ($sub_arr['data'] as $key => $value) {
                if (is_array($value) && !empty($value)) {
                    $sub_arrs[] = [
                        'append' => implode('.', array_filter([trim($sub_arr['append']), trim($key)])),
                        'data'   => $value,
                    ];
                } else {
                    $keys[] = implode('.', array_filter([trim($sub_arr['append']), trim($key)]));
                }
            }
        }
        return $keys;
    }

    /**
     * Determines whether an array contains a value(s).
     *
     * @param array $arr
     * @param mixed $value
     * @return bool
     */
    public static function contains($arr, $value)
    {
        $values = is_array($value) ? $value : [$value];
        $intersection = array_intersect(array_keys($arr), $values);
        sort($intersection);
        return $intersection == $values;
    }

    /**
     * Filters an array with a given callback.
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     */
    public static function filter($arr, $callback)
    {
        return array_filter($arr, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Gets the first element from an array.
     *
     * @param array $arr
     * @param null|callable $callback An optional callback that can be provided that must return true before an element
     * is returned; takes the value and then the key of each element in the array.
     * @return mixed
     */
    public static function first($arr, $callback = null)
    {
        $item = null;
        foreach ($arr as $key => $item) {
            if (is_null($callback) || $callback($item, $key) === true) {
                break;
            }
        }
        return $item;
    }

    /**
     * Gets the first element from an array.
     *
     * @param array $arr
     * @return array|null
     */
    public function head($arr)
    {
        return self::first($arr);
    }

    /**
     * Gets an array minus the first element.
     *
     * @param array $arr
     * @return array
     */
    public static function tail($arr)
    {
        return array_slice($arr, 1);
    }
}