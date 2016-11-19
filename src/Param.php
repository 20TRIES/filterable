<?php

namespace _20TRIES\Filterable;

/**
 * Placeholder that represents a request parameter.
 *
 * @package _20TRIES\Filterable
 */
class Param
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the name of the parameter.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }
}