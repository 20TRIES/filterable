<?php

namespace _20TRIES\Filterable;

use _20TRIES\Filterable\Exceptions\InvalidConfigurationException;

/**
 * Adapts user input to securely build a query.
 *
 * @package _20TRIES\Filterable\Adaptors
 */
class Adaptor
{
    /**
     * Handles the adaption.
     *
     * @param array $configuration
     * @param array $input
     * @param mixed $query
     * @return mixed
     */
    public function adapt($configuration, $input, $query)
    {
        foreach ($this->parseConfiguration($configuration, $input) as $filter_key => $configuration) {
            $method = $configuration[0];
            $params = $this->arrOnly($input, array_slice($configuration, 1));
            $query = $method($query, ...$params);
        }
        return $query;
    }

    // untested
    public function adaptSet($set, $input, $query)
    {
        $compiler = new Compiler;

        $compiled = $compiler->compile($set);

        $config = $this->filterConfigToInput($compiled, $input);

        $configuration_item = null;

        foreach ($config as $configuration_item) {
            break;
        }

        $method = $configuration_item[0];

        $params = $this->arrOnly($input, array_slice($configuration_item, 1));

        return $method($query, ...$params);
    }

    protected function filterConfigToInput($configurations, $input)
    {
        $all_input_keys = $this->arrKeys($input);
        $parsed_configurations = [];

        foreach ($configurations as $key => $configuration) {
            // Skip empty configurations.
            if (empty($configuration)) {
                continue;
            }

            if (! is_numeric($key)) {
                // If the request params do not match the params provided in the filter key; continue.
                $name_params = array_filter(explode(',', $key));
                $intersection = array_intersect($all_input_keys, $name_params);
                sort($intersection);
                if ($intersection != $name_params) {
                    continue;
                }

                foreach (array_slice($configuration, 1) as $param) {
                    if ($param instanceof Param && !in_array($param->name(), $name_params)) {
                        throw new InvalidConfigurationException('Scope parameters must be included within the configuration key');
                    }
                }
            }

            $parsed_configurations[$key] = $configuration;
        }

        return $parsed_configurations;
    }

    /**
     * Parses a set of configuration rules.
     *
     * @param array $raw_configurations
     * @return array
     * @throws InvalidConfigurationException
     */
    public function parseConfiguration($raw_configurations, $input)
    {
        $configurations = (new Compiler($this))->compile($raw_configurations);
        $all_input_keys = $this->arrKeys($input);
        $parsed_configurations = [];

        foreach ($configurations as $key => $configuration) {
            // Skip empty configurations.
            if (empty($configuration)) {
                continue;
            }

            if (! is_numeric($key)) {
                // If the request params do not match the params provided in the filter key; continue.
                $name_params = array_filter(explode(',', $key));
                $intersection = array_intersect($all_input_keys, $name_params);
                sort($intersection);
                if ($intersection != $name_params) {
                    continue;
                }

                foreach (array_slice($configuration, 1) as $param) {
                    if ($param instanceof Param && !in_array($param->name(), $name_params)) {
                        throw new InvalidConfigurationException('Scope parameters must be included within the configuration key');
                    }
                }
            }

            $parsed_configurations[$key] = $configuration;
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
    protected function arrGet($arr, $parameter, $default = null)
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
    protected function arrOnly($arr, $attributes)
    {
        $data = [];
        foreach ($attributes as $parameter) {
            $data[] = $parameter instanceof Param
                ? $this->arrGet($arr, $parameter->name())
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
    protected function arrKeys($arr) {
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