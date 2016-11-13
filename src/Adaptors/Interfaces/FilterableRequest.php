<?php

namespace _20TRIES\Filterable\Adaptors\Interfaces;

/**
 * An interface for requests that are filterable.
 *
 * @package _20TRIES\Filterable\Adaptors\Interfaces
 */
interface FilterableRequest
{
    /**
     * Gets the scopes configured for a request.
     *
     * @return array
     */
    public function scopes();
}