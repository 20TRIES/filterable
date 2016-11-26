<?php

namespace _20TRIES\Filterable;


use _20TRIES\Filterable\Exceptions\InvalidConfigurationException;

class Compiler
{

    protected $adaptor;

    public function __construct($adaptor = null)
    {
        $this->adaptor = is_null($adaptor) ? new Adaptor : $adaptor;
    }

    public function compile($configurations)
    {

        foreach($configurations as $key => $configuration) {

            // Expand configuration sets into main configuration
            if (is_numeric($key)) {
                $matched = false;
                $set = [];
                $params = [];
                foreach ($configuration as $configuration_item_key => $configuration_item) {

                    // If we reach the default element
                    if ($configuration_item_key === "" && $matched === false) {
                        $set[$configuration_item_key] = $configuration_item;
                        $matched = true;
                        continue;
                    }

                    $name_params = array_filter(explode(',', $configuration_item_key));

                    array_push($params, ...$name_params);

                    // If the key matches one already present in the main configuration set
                    if(array_key_exists($configuration_item_key, $configurations)) {
                        throw new InvalidConfigurationException('Duplicated filter');
                    }
                    $set[$configuration_item_key] = $configuration_item;
                }

                $configurations[$key] = [];

                $input_keys = array_unique($params);

                $configurations[$key][] = function ($query, ...$input) use ($set, $input_keys) {
                    return $this->adaptor->adaptSet($set, array_filter(array_combine($input_keys, $input)), $query);
                };
                foreach ($input_keys as $name) {
                    $configurations[$key][] = new Param($name);
                }
            }
        }

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
                $parsed_configurations[$parsed_key] = [$configuration];
            } elseif(is_numeric($key)) {
                $parsed_configurations[] = $configuration;
            } else {
                $parsed_configurations[$parsed_key] = $configuration;
            }
        }

        return $parsed_configurations;
    }
}