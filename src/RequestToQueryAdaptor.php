<?php

namespace _20TRIES\Filterable;

use _20TRIES\Filterable\Interfaces\FilterableRequest;
use Symfony\Component\HttpFoundation\Request;
use _20TRIES\Filterable\Exceptions\InvalidConfigurationException;

/**
 * A request adaptor that adapts a request query to an Eloquent query.
 *
 * @package _20TRIES\Filterable\Adaptors
 */
class RequestToQueryAdaptor
{
    /**
     * Handles the adaption.
     *
     * @param Request $request
     * @param mixed $query
     * @return mixed
     */
    public static function adapt(Request $request, $query)
    {
        foreach (self::getConfiguration($request) as $filter_key => $configuration) {
            $method = $configuration[0];
            $params = self::getDataSetFromRequest($request, array_slice($configuration, 1));
            $query = $method($query, ...$params);
        }
        return $query;
    }

    /**
     * Gets the configuration for an adaptor, from a request.
     *
     * @param FilterableRequest $request
     * @return array
     * @throws InvalidConfigurationException
     */
    public static function getConfiguration(FilterableRequest $request)
    {
        $raw_configurations = $request->scopes();
        $configurations = self::preCompile($raw_configurations);
        $all_input = self::getAllDataFromRequest($request);
        $all_input_keys = self::getArrayKeys($all_input);
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
     * Pre-compiles a set of scope configurations.
     *
     * @param $configurations
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
     * @param Request $request
     * @param string $parameter
     * @param null $default
     * @return mixed|null
     */
    protected static function getDataFromRequest(Request $request, $parameter, $default = null)
    {
        $components = array_filter(explode('.', trim($parameter)));
        $input = $request->get(head($components));
        foreach (array_slice($components, 1) as $component) {
            if (array_key_exists($component, $input)) {
                $input = $input[$component];
            } else {
                return $default;
            }
        }
        return $input;
    }

    /**
     * Gets a set of parameters from a request.
     *
     * @param Request $request
     * @param array $parameters
     * @return array
     */
    protected static function getDataSetFromRequest(Request $request, $parameters)
    {
        $data = [];
        foreach ($parameters as $parameter) {
            $data[] = $parameter instanceof Param
                ? self::getDataFromRequest($request, $parameter->name())
                : $parameter;
        }
        return $data;
    }

    /**
     * Gets all data from a request.
     *
     * @param Request $request
     * @return array
     */
    protected static function getAllDataFromRequest(Request $request)
    {
        $input_bag = $request->getMethod() == 'GET' ? $request->query : $request->request;
        return $input_bag->all();
    }

    /**
     * Gets the keys available in a "dot notation" array.
     *
     * @param array $arr
     * @return array
     */
    protected static function getArrayKeys($arr) {
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