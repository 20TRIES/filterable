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
     * Extracts all request parameters from a filter key.
     *
     * @param string $filter_key
     * @return array
     */
    protected function getParams($filter_key) {
        $params = explode(',', trim($filter_key));
        foreach ($params as $key => $param) {
            $params[$key] = trim($param);
        }
        return $params;
    }

    /**
     * Gets the configuration for an adaptor, from a request.
     *
     * @param FilterableRequest $request
     * @return array
     * @throws InvalidConfigurationException
     */
    public function getConfiguration(FilterableRequest $request) {

        $configurations = $request->scopes();

        $all_input = $this->getAllDataFromRequest($request);

        $parsed_configurations = [];

        foreach ($configurations as $filter_key => $configuration) {

            // If the request params do not match the params provided in the filter key; continue.
            $name_params = array_filter(explode(',', $filter_key));
            sort($name_params);
            $intersection = array_intersect(array_keys($all_input), $name_params);
            sort($intersection);
            if ($intersection != $name_params) {
                continue;
            }

            // Get input that is available to the callback function.
            $input = array_intersect_key($all_input, array_flip($name_params));

            // Get parsed key.
            $parsed_key = implode(',', $name_params);
            if (array_key_exists($parsed_key, $parsed_configurations)) {
                throw new InvalidConfigurationException('Duplicated filter');
            }

            // Get Callback
            if (is_string($configuration)) {
                $parsed_configurations[$parsed_key] = [];
                $method = preg_replace('/[^(]*\K.*/si', '', $configuration);
                $params = array_filter(explode(',', preg_replace('/^[^(]*\({0,}|\)$/si', '', $configuration)));
                $parsed_configurations[$parsed_key][] = function ($query, ...$params) use ($method) {
                    return $query->$method(...$params);
                };

                // Get params.
                foreach ($params as $key => $param) {
                    $param = trim($param);

                    // If the parameter is a string literal.
                    if (in_array(substr($param, 0, 1), ['"', '\''])) {
                        $param = substr($param, 1, -1);
                    }

                    // If the parameter is a numeric literal.
                    elseif (is_numeric($param)) {
                        $param = ((float)$param - (int)$param) > 0 ? (float)$param : (int)$param;
                    }

                    // If the parameter is a input parameter reference.
                    elseif(array_key_exists($param, $input)) {
                        $param = new Param($param);
                    }

                    else {
                        throw new InvalidConfigurationException('Scope parameters must be included within the configuration key');
                    }
                    $parsed_configurations[$parsed_key][] = $param;
                }
            } elseif (is_array($configuration)) {
                $parsed_configurations[$parsed_key] = $configuration;
            } elseif (is_callable($configuration)) {
                $parsed_configurations[$parsed_key] = [$configuration];
            } else {
                throw new InvalidConfigurationException('Unable to generate a filter for the given scope.');
            }
        }

        return $parsed_configurations;
    }

    /**
     * Resolves a filter from a method name or alias.
     *
     * @param HasFilters $request
     * @param string $filter_name
     * @return Filter|null
     */
    protected function resolveFilter(HasFilters $request, $filter_name) {
        $config = $this->getConfiguration($request);
        return array_key_exists($filter_name, $config)
            ? $this->getFilterInstance($request, $filter_name, $config[$filter_name])
            : null;
    }

    /**
     * Gets a filter object from a scope's config.
     *
     * @param HasFilters $request
     * @param string $scope
     * @param array|string $config
     * @return Filter
     * @throws InvalidConfigurationException
     */
    protected function getFilterInstance(HasFilters $request, $scope, $config)
    {
        if (is_string($config) || is_callable($config)) {
            $filter = new Filter();
            $filter->setMethod($config);
        } elseif ($config instanceof Filter) {
            $filter = $config;
        } else {
            throw new InvalidConfigurationException('Unable to generate a filter for the given scope.');
        }
        return $filter;
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