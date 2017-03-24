<?php namespace _20TRIES\Filterable;

/**
 * Placeholder that represents a set of request parameters.
 *
 * @package _20TRIES\Filterable
 */
class ParamSet
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * Constructor.
     *
     * @param string[] $names
     */
    public function __construct(...$names)
    {
        $this->parameters = array_map(function ($name) {
            return new Param($name);
        }, $names);
    }

    /**
     * Gets the parameters.
     *
     * @return string
     */
    public function parameters()
    {
        return $this->parameters;
    }
}