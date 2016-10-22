<?php

namespace _20TRIES\Filterable\Adaptors;

use _20TRIES\Filterable\Adaptors\Interfaces\Request;

/**
 * A request adaptor that adapts a request query to an Eloquent query.
 *
 * @package _20TRIES\Filterable\Adaptors
 */
abstract class RequestToQueryAdaptor
{
    /**
     * @var string The adaptors trait.
     */
    protected static $trait;

    /**
     * @var string The name of the query string parameter that should be used.
     */
    protected $parameter;

    /**
     * Constructor.
     *
     * @param string $parameter
     */
    public function __construct($parameter = null)
    {
        $this->parameter = is_null($parameter) ? $this->parameter : $parameter;
    }

    /**
     * Handles the adaption.
     *
     * @param Request $request
     * @param mixed $query
     */
    public abstract function adapt(Request $request, $query);

    /**
     * Gets the filters passed as input from a request.
     *
     * @param Request $request
     * @return mixed
     */
    protected function getDataFromRequest(Request $request)
    {
        return $request->get($this->parameter);
    }

    /**
     * Gets the configuration for an adaptor, from a request.
     *
     * @param mixed $request
     * @return array
     */
    protected abstract function getConfiguration($request);
}