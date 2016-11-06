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
     * Gets the configuration for an adaptor, from a request.
     *
     * @param mixed $request
     * @return array
     */
    protected abstract function getConfiguration($request);

    /**
     * Gets the name of the parameter used by an adaptor to extract data from a request.
     *
     * @return null|string
     */
    public function getParameter()
    {
        return $this->parameter;
    }
}