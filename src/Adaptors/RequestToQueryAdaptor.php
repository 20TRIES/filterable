<?php

namespace _20TRIES\Filterable\Adaptors;

use _20TRIES\Filterable\Adaptors\Interfaces\FilterableRequest;
use Symfony\Component\HttpFoundation\Request;
use _20TRIES\Filterable\Exceptions\InvalidConfigurationException;
use _20TRIES\Filterable\Param;

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
    public function adapt(Request $request, $query)
    {
        foreach ($this->getConfiguration($request) as $filter_key => $configuration) {
            $method = $configuration[0];
            $params = $this->getDataSetFromRequest($request, array_slice($configuration, 1));
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
    public function getConfiguration(FilterableRequest $request)
    {
        $raw_configurations = $request->scopes();
        $configurations = $this->preCompile($raw_configurations);
        $all_input = $this->getAllDataFromRequest($request);
        $parsed_configurations = [];
        foreach ($configurations as $key => $configuration) {
            // If the request params do not match the params provided in the filter key; continue.
            $name_params = array_filter(explode(',', $key));
            $intersection = array_intersect(array_keys($all_input), $name_params);
            sort($intersection);
            if ($intersection != $name_params) {
                continue;
            }
            $input = array_intersect_key($all_input, array_flip($name_params));
            foreach (array_slice($configuration, 1) as $param) {
                if ($param  instanceof Param && !array_key_exists($param->name(), $input)) {
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
    public function preCompile($configurations)
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
    protected function getDataFromRequest(Request $request, $parameter, $default = null)
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
    protected function getDataSetFromRequest(Request $request, $parameters)
    {
        $data = [];
        foreach ($parameters as $parameter) {
            $data[] = $parameter instanceof Param
                ? $this->getDataFromRequest($request, $parameter->name())
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
    protected function getAllDataFromRequest(Request $request)
    {
        return ($request->getMethod() == 'GET' ? $request->query : $request->request)->all();
    }
}