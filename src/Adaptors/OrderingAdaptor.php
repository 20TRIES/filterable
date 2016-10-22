<?php

namespace _20TRIES\Filterable\Adaptors;

use _20TRIES\Filterable\Adaptors\Interfaces\Request;
use _20TRIES\Filterable\Adaptors\Interfaces\HasOrderings;

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
     */
    public function adapt(Request $request, $query)
    {
        // TODO: Implement adapt() method.
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