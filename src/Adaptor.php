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
            $params = (new Arr($input))->only(array_slice($configuration, 1));
            $query = $method($query, ...$params);
        }
        return $query;
    }

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

        $params = (new Arr($input))->only(array_slice($configuration_item, 1));

        return $method($query, ...$params);
    }

    protected function filterConfigToInput($configurations, $input)
    {
        $all_input_keys = (new Arr($input))->keys();
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
        $all_input_keys = (new Arr($input))->keys();
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
}