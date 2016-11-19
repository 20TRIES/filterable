<?php

namespace _20TRIES\Filterable;

use _20TRIES\Filterable\Exceptions\InvalidConfigurationException;

/**
 * Adapts user input to securely build a query.
 *
 * @package _20TRIES\Filterable\Adaptors
 */
class RequestToQueryAdaptor
{
    /**
     * Handles the adaption.
     *
     * @param array $configuration
     * @param array $input
     * @param mixed $query
     * @return mixed
     */
    public static function adapt($configuration, $input, $query)
    {
        foreach (self::parseConfiguration($configuration, $input) as $filter_key => $configuration) {
            $method = $configuration[0];
            $params = self::arrOnly($input, array_slice($configuration, 1));
            $query = $method($query, ...$params);
        }
        return $query;
    }

    /**
     * Parses a set of configuration rules.
     *
     * @param array $raw_configurations
     * @return array
     * @throws InvalidConfigurationException
     */
    public static function parseConfiguration($raw_configurations, $all_input)
    {
        $configurations = self::preCompile($raw_configurations);
        $all_input_keys = self::arrKeys($all_input);
        $parsed_configurations = [];
        foreach ($configurations as $key => $configuration) {
            // If the request params do not match the params provided in the filter key; continue.
            $name_params = array_filter(explode(',', $key));
            $intersection = array_intersect($all_input_keys, $name_params);
            sort($intersection);
            if ($intersection != $name_params) {
                continue;
            }
            $input_keys = array_intersect($all_input_keys, $name_params);
            foreach (array_slice($configuration, 1) as $param) {
                if ($param  instanceof Param && !in_array($param->name(), $input_keys)) {
                    throw new InvalidConfigurationException('Scope parameters must be included within the configuration key');
                }
            }
            $parsed_configurations[$key] = $configuration;
        }
        return $parsed_configurations;
    }

    /**
     * Pre-compiles a set of configuration rules.
     *
     * @param array $configurations
     * @return array
     * @throws InvalidConfigurationException
     */
    public static function preCompile($configurations)
    {
        $parsed_configurations = [];
        foreach ($configurations as $key => $configuration) {
            $name_params = array_filter(explode(',', $key));
            sort($name_params);
            $parsed_key = trim(implode(',', $name_params));
            if (array_key_exists($parsed_key, $parsed_configurations)) {
                throw new InvalidConfigurationException('Duplicated filter');
            }

            if (is_string($configuration)) {
                $parsed_configurations[$parsed_key] = [];
                $method = preg_replace('/[^(]*\K.*/si', '', $configuration);
                $params = array_filter(explode(',', preg_replace('/^[^(]*\({0,}|\)$/si', '', $configuration)));
                $parsed_configurations[$parsed_key][] = function ($query, ...$params) use ($method) {
                    return $query->$method(...$params);
                };
                foreach ($params as $key => $param) {
                    $param = trim($param);
                    if (in_array(substr($param, 0, 1), ['"', '\''])) {
                        $param = substr($param, 1, -1);
                    } elseif (is_numeric($param)) {
                        $param = ((float)$param - (int)$param) > 0 ? (float)$param : (int)$param;
                    } elseif ($param === 'true' || $param === 'false') {
                        $param = $param === 'true';
                    } else {
                        $param = new Param($param);
                    }
                    $parsed_configurations[$parsed_key][] = $param;
                }
            } elseif(is_callable($configuration)) {
                $parsed_configurations[$key] = [$configuration];
            } else {
                $parsed_configurations[$parsed_key] = $configuration;
            }
        }
        return $parsed_configurations;
    }

    /**
     * Gets a parameter from a request.
     *
     * @param array $arr
     * @param string $parameter
     * @param null $default
     * @return null
     */
    protected static function arrGet($arr, $parameter, $default = null)
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
     * @param array $arr
     * @param array $attributes
     * @return array
     */
    protected static function arrOnly($arr, $attributes)
    {
        $data = [];
        foreach ($attributes as $parameter) {
            $data[] = $parameter instanceof Param
                ? self::arrGet($arr, $parameter->name())
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
    protected static function arrKeys($arr) {
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
}