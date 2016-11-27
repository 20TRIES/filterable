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
     * @param string $parameter
     * @param null $default
     * @return mixed
     */
    public static function get($arr, $parameter, $default = null)
    {
        $components = array_filter(explode('.', trim($parameter)));
        $head = array_shift($components);
        $input = array_key_exists($head, $arr) ? $arr[$head] : $default;
        if (! is_null($input)) {
            foreach ($components as $component) {
                if (array_key_exists($component, $input)) {
                    $input = $input[$component];
                } else {
                    return $default;
                }
            }
        }
        return $input;
    }

    /**
     * Determines whether an array has a given key.
     *
     * @param array $arr
     * @param string $key
     * @return bool
     */
    public static function has($arr, $key)
    {
        $components = array_filter(explode('.', trim($key)));
        foreach ($components as $component) {
            if (array_key_exists(trim($component), $arr)) {
                $arr = $arr[$component];
            } else {
                return false;
            }
        }
        return ! empty($components);
    }

    /**
     * Gets a set of attribute from an array using "dot notation".
     *
     * @param array $arr
     * @param array $attributes
     * @return array
     */
    public static function only($arr, $attributes)
    {
        $data = [];
        foreach ($attributes as $parameter) {
            $data[] = $parameter instanceof Param
                ? self::get($arr, $parameter->name())
                : $parameter;
        }
        return $data;
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