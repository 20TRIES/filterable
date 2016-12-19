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
     * Determines whether an array has a given set of keys.
     *
     * @param array $arr
     * @param string|int|array $keys
     * @param mixed $value The value of the given key if only a single key is provided.
     * @return bool
     */
    public static function has($arr, $keys, &$value = null)
    {
        if (is_array($keys) && count($keys) > 1) {
            // Determine whether $arr contains all keys in the $keys array
            return empty(array_diff(is_array($keys) ? $keys : [$keys], self::keys($arr)));
        } elseif(is_array($keys) && empty($arr)) {
            // Only possible true result would be if $keys is and array and is empty.
            return is_array($keys) && empty($keys);
        } elseif(is_array($keys)) {
            // If $keys only has one element, we will optimise it down to a string so that we can do a single key
            // lookup.
            $keys = self::head($keys);
        }
        foreach (self::splitKey($keys) as $key) {
            if (is_array($arr) && array_key_exists($key, $arr)) {
                $arr = $arr[$key];
            } else {
                return false;
            }
        }
        $value = $arr;
        return true;
    }


    /**
     * Gets a set of attributes from an array.
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
     * Gets the keys available in an array.
     *
     * @param array $arr
     * @return array
     */
    public static function keys($arr)
    {
        $output = [];
        self::traverse($arr, function ($item, $key) use (&$output) {
            $output[] = $key;
        });
        return $output;
    }

    /**
     * Traverses each item of a multidimensional array.
     *
     * @param array $arr
     * @param callable $callback
     * @return array
     */
    protected static function traverse($arr, $callback)
    {
        $sets = [['append' => '', 'items' => $arr]];
        $output = [];
        Arr::reduce($sets, function ($sub_arr) use (&$sets, &$output, $callback) {
            Arr::reduce($sub_arr['items'], function ($value, $local_key) use (&$sets, &$output, $callback, $sub_arr) {
                $full_key = Arr::buildKey($sub_arr['append'], $local_key);
                if (is_array($value) && !empty($value)) {
                    array_push($sets,
                        ['append' => $sub_arr['append'], 'items'  => $sub_arr['items']],
                        ['append' => $full_key, 'items' => $value]
                    );
                    return false;
                }
                $output[] = $callback($value, $full_key);
            });
        });
        return $output;
    }

    /**
     * Splits a key into its components for "dot notation".
     *
     * @param string|int $key
     * @return array
     */
    protected static function splitKey($key)
    {
        return is_string($key) ? explode('.', $key) : [$key];
    }

    /**
     * Builds a dot notation style array key.
     *
     * @param array ...$components
     * @return string
     */
    public static function buildKey(...$components)
    {
        return implode('.', Arr::filter($components, function ($item) {
            return ! is_string($item) || $item !== '';
        }));
    }

    /**
     * Iteratively reduce an array applying a callback function to each of the elements removed.
     *
     * @param array $arr
     * @param callable $callback
     * @return bool Returns true if all items were reduced, or false if reduction was halted early.
     */
    public static function reduce(&$arr, $callback)
    {
        while (! empty($arr)) {
            end($arr);
            $key = key($arr);
            if($callback(array_pop($arr), $key) === false) {
                return false;
            };
        }
        return true;
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
    public static function head($arr)
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