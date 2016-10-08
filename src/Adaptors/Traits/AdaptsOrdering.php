<?php

namespace _20TRIES\Filterable\Adaptors\Traits;


interface AdaptsOrdering
{
    /**
     * An array of orderings that can be applied to a query.
     *
     * @return array
     */
    public function orderings();
}