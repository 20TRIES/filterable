<?php namespace _20TRIES\Filterable;

use _20TRIES\Filterable\Exceptions\InvalidConfigurationException;

/**
 * Compiler for filterable configuration sets.
 *
 * @package _20TRIES\Filterable
 */
class Compiler
{
    /**
     * @var Adaptor|null
     */
    protected $adaptor;

    /**
     * @var string
     */
    protected $default_key = '';

    /**
     * Constructor.
     *
     * @param null $adaptor
     */
    public function __construct($adaptor = null)
    {
        $this->adaptor = is_null($adaptor) ? new Adaptor : $adaptor;
    }

    /**
     * Compiles array of configuration sets.
     *
     * @param $configs
     * @return array
     * @throws InvalidConfigurationException
     */
    public function compile($configs)
    {
        // Compile configuration sets.
        foreach($configs as $key => $configuration) {
            if (is_numeric($key)) {
                $configs[$key] = $this->wrapFilterSet($configuration, $configs);
            }
        }

        $parsed_configurations = [];
        foreach ($configs as $key => $configuration) {
            $name_params = array_filter(explode(',', $key));
            sort($name_params);
            $parsed_key = trim(implode(',', $name_params));
            if (array_key_exists($parsed_key, $parsed_configurations)) {
                throw new InvalidConfigurationException('Duplicated configuration item');
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
                        if (!in_array($param->name(), $name_params)) {
                            throw new InvalidConfigurationException('Scope parameters must be included within the configuration key');
                        }
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

        // Return compiled configuration filtered of any placeholders (null elements).
        return array_filter($parsed_configurations);
    }

    /**
     * Wraps a filter set into a compiled configuration item and generates placeholder items for sub-items within the
     * set.
     *
     * @param array $set
     * @param array $configs
     * @return array
     * @throws InvalidConfigurationException
     */
    protected function wrapFilterSet($set, &$configs)
    {
        $compiled_set = $this->compile($set);
        $wrapped_set = [];
        $params = [];

        foreach ($set as $key => $config) {
            if ($key !== $this->default_key) {
                array_push($params, ...array_filter(explode(',', $key)));

                // If the key matches one already present in the main configuration set
                if(array_key_exists($key, $configs)) {
                    throw new InvalidConfigurationException('Duplicated configuration item');
                }

                // Set placeholder element in main configuration set to catch duplicates
                $configs[$key] = null;
            }
        }

        // Add callback to wrapper configuration item
        $input_keys = array_unique($params);
        $wrapped_set[] = function ($query, ...$input) use ($compiled_set, $input_keys) {
            // Combine input keys with input provided via parameters.
            $input = array_combine($input_keys, $input);

            // Adapt the query using the compiled configuration set.
            return $this->adaptor->adaptSet($compiled_set, array_filter($input), $query);
        };

        // Add parameters to wrapper configuration item
        foreach ($input_keys as $name) {
            $wrapped_set[] = new Param($name);
        }

        return $wrapped_set;
    }
}