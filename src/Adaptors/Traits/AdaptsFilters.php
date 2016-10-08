<?php

namespace _20TRIES\Filterable\Adaptors\Traits;

interface AdaptsFilters
{
    /**
     * An array of filters that can be applied to a query.
     *
     * @return array
     */
    public function filters();
}