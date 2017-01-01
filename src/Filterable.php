<?php namespace _20TRIES\Filterable;

/**
 * A trait that can be used to allow an object to apply a set of filtering to a query.
 *
 * @package _20TRIES\Filterable
 */
trait Filterable
{
    /**
     * Gets any filterings that are available.
     *
     * @return array
     */
    protected function filtering()
    {
        return [

        ];
    }

    /**
     * Applies any filtering to a query for a given set of input.
     *
     * @param mixed $query
     * @param array $input
     * @return mixed
     */
    protected function applyFiltering($query, $input = [])
    {
        $config = (new Compiler)->compile($this->filtering());
        return (new Adaptor())->adapt($config, $input, $query);
    }
}