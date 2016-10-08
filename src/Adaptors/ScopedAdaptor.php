<?php

namespace _20TRIES\Filterable\Adaptors;

use _20TRIES\Filterable\Exceptions\InvalidConfigurationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * An adaptor that is based around model query scopes.
 */
class ScopedAdaptor extends RequestAdaptor
{
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
    public function handle(Request $request, $query) {
        foreach ($request->get($this->parameter, []) as $name => $value) {

            $method = $this->resolveQueryScope($this->getScopes($request), $name);

            if ( ! is_null($method)) {

                // If the method is a closure, then we will call it and pass it the query and $value.
                if (is_callable($method)) {
                    $query = $method($query, $value);
                }

                // Otherwise we will assume that it is a method name and will call it against the query and pass it the
                // $value.
                else {
                    $query = $query->$method($value);
                }
            }
        }
    }

    /**
     * Gets the configuration for any available scopes.
     *
     * @param Request $request
     * @return array
     */
    protected function getScopes($request) {
        return $request->scopes();
    }

    /**
     * Resolves a query scope from a method name or alias.
     *
     * @param array $scopes
     * @param string $name
     * @return callable|mixed|null|string
     */
    protected function resolveQueryScope($scopes, $name) {
        if (array_key_exists($name, $scopes)) {
            return $this->getScopeMethod($scopes[$name]);
        }
        return null;
    }

    /**
     * Gets the method name from a scope's config.
     *
     * @param mixed $scope
     * @return callable|mixed|string
     * @throws InvalidConfigurationException
     */
    protected function getScopeMethod($scope)
    {
        // If the scope configuration is a string, then we will assume that this is the name of the method.
        if (is_string($scope) || is_callable($scope)) {
            $method = $scope;
        }

        // If the scope configuration is an array, then we will look for a "method" element within the array.
        elseif (is_array($scope) && array_key_exists('method', $scope)) {
            $method = $scope['method'];
        }

        else {
            throw new InvalidConfigurationException('Unable to determine the method for a given scope.');
        }

        return $method;
    }
}