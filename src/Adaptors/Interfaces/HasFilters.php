<?php

namespace _20TRIES\Filterable\Adaptors\Interfaces;

/**
 * An interface for a request that is filterable.
 *
 * @package _20TRIES\Filterable\Adaptors\Traits
 */
interface HasFilters extends Request
{
    /**
     * Gets the filters that are configured for a filterable request.
     *
     * @return array
     */
    public function filters();
}