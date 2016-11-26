<?php

namespace _20TRIES\Filterable;


class Arr
{
    protected $items = [];


    public function __construct($items = [])
    {
      $this->items = $items;
    }

    /**
     * Gets a parameter from a request.
     *
     * @param string $parameter
     * @param null $default
     * @return null
     */
    public function get($parameter, $default = null)
    {
        $components = array_filter(explode('.', trim($parameter)));
        $head = array_shift($components);
        $input = array_key_exists($head, $this->items) ? $this->items[$head] : $default;
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
    public function only($attributes)
    {
        $data = [];
        foreach ($attributes as $parameter) {
            $data[] = $parameter instanceof Param
                ? $this->get($parameter->name())
                : $parameter;
        }
        return $data;
    }

    /**
     * Gets the keys available in a "dot notation" array.
     *
     * @return array
     */
    public function keys() {
        $keys = [];
        $sub_arrs = [['append' => '', 'data' => $this->items]];
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
}