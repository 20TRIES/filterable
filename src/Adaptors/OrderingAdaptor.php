<?php

namespace _20TRIES\Filterable\Adaptors;

use _20TRIES\Filterable\Adaptors\Interfaces\Request;
use _20TRIES\Filterable\Adaptors\Interfaces\HasOrderings;
use _20TRIES\Filterable\Exceptions\InvalidConfigurationException;

/**
 * Adapts a request query to a query builder instance.
 */
class OrderingAdaptor extends RequestToQueryAdaptor
{
    /**
     * @var string The adaptors trait.
     */
    protected static $trait = HasOrderings::class;

    /**
     * @var string The name of the query string parameter that should be used.
     */
    protected $parameter = 'order';

    /**
     * Handles the adaption.
     *
     * @param Request $request
     * @param mixed $query
     * @return mixed
     */
    public function adapt(Request $request, $query)
    {
        $ordering = $this->resolveOrdering($request, $this->getDataFromRequest($request));
        return ! is_null($ordering) ? $ordering($query) : $query;
    }

    /**
     * Resolves a filter from a method name or alias.
     *
     * @param HasOrderings $request
     * @param string $ordering_name
     * @return mixed
     */
    protected function resolveOrdering(HasOrderings $request, $ordering_name) {
        $config = $this->getConfiguration($request);
        return array_key_exists($ordering_name, $config) ? $this->getOrderingInstance($config[$ordering_name]) : null;
    }

    /**
     * Gets a closure from an ordering configuration.
     *
     * @param array|string $config
     * @return callable
     * @throws InvalidConfigurationException
     */
    protected function getOrderingInstance($config)
    {
        if (is_array($config)) {
            $method = $config[0];
            $params = array_slice($config, 1);
            return function ($query) use ($method, $params) {
                return $query->$method(...$params);
            };
        } elseif (is_callable($config)) {
            return $config;
        }
        throw new InvalidConfigurationException(
            'Unable to generate a ordering closure from the provided ordering configuration.'
        );
    }

    /**
     * Gets the configuration for an adaptor, from a request.
     *
     * @param $request
     * @return array
     */
    protected function getConfiguration($request) {
        return $request->orderings();
    }
}