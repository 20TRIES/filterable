<?php

namespace _20TRIES\Filterable\Adaptors;

use _20TRIES\Filterable\Adaptors\Traits\AdaptsFilters;
use Symfony\Component\HttpFoundation\Request;

/**
 * Adapts a request query to a query builder instance.
 */
class FilteringAdaptor extends ScopedAdaptor
{
    /**
     * @var string The adaptors trait.
     */
    protected static $trait = AdaptsFilters::class;

    /**
     * @var string The name of the query string parameter that should be used.
     */
    protected $parameter = 'filters';

    /**
     * Gets the configuration for any available scopes.
     *
     * @param Request $request
     * @return array
     */
    protected function getScopes(Request $request) {
        return $request->filters();
    }
}