<?php

namespace _20TRIES\Filterable;


class Arr
{
    /**
     * Gets a parameter from a request.
     *
     * @param string $parameter
     * @param null $default
     * @return null
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
     * Gets a set of attribute from an array using "dot notation".
     *
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
     * @return array
     */
    public static function keys($arr) {
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
     * @param mixed A value or array of values.
     * @return bool
     */
    public static function contains($arr, $value) {
        $values = is_array($value) ? $value : [$value];
        $intersection = array_intersect(array_keys($arr), $values);
        sort($intersection);
        return $intersection == $values;
    }

    public static function filter($arr, $callback)
    {
        return array_filter($arr, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * @param array $arr
     * @param null|callable $callback
     * @return mixed
     */
    public static function first($arr, $callback = null) {
        $item = null;
        foreach ($arr as $key => $item) {
            if (is_null($callback) || $callback($item, $key) === true) {
                break;
            }
        }
        return $item;
    }

    public static function tail($arr) {
        return array_slice($arr, 1);
    }
}