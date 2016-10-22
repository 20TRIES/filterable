<?php

namespace _20TRIES\Filterable\Adaptors;

use _20TRIES\Filterable\Adaptors\Interfaces\HasFilters;
use _20TRIES\Filterable\Adaptors\Interfaces\Request;
use _20TRIES\Filterable\Exceptions\InvalidConfigurationException;
use _20TRIES\Filterable\Filters\Filter;

/**
 * An adaptor that translates filters from the input of a request to a query.
 */
class FilteringAdaptor extends RequestToQueryAdaptor
{
    /**
     * @var string The adaptors trait.
     */
    protected static $trait = HasFilters::class;

    /**
     * Handles the adaption.
     *
     * @param Request $request
     * @param mixed $query
     */
    public function adapt(Request $request, $query) {
        foreach ($this->getDataFromRequest($request) as $name => $value) {
            $filter = $this->resolveFilter($request, $name);
            if ( ! is_null($filter)) {
                $method = $filter->getMethod();
                $query = $method($query, ...$filter->getMutatedValues());
            }
        }
    }

    /**
     * Gets the configuration for an adaptor, from a request.
     *
     * @param mixed $request
     * @return array
     */
    protected function getConfiguration($request) {
        return $request->filters();
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
            $filter->setValues($this->getFilterInput($scope, $request));
        } elseif ($config instanceof Filter) {
            $filter = $config;
        } else {
            throw new InvalidConfigurationException('Unable to generate a filter for the given scope.');
        }
        return $filter;
    }

    /**
     * Gets any input, provided within a request, for a given filter.
     *
     * @param string $filter
     * @param HasFilters $request
     * @return null|array
     */
    protected function getFilterInput($filter, HasFilters $request)
    {
        $filters = $this->getDataFromRequest($request);
        if (array_key_exists($filter, $filters)) {
            return is_array($filters[$filter]) ? $filters[$filter] : [$filters[$filter]];
        } else {
            return [];
        }
    }
}