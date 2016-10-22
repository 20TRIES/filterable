<?php

namespace _20TRIES\Filterable\Adaptors\Interfaces;


interface HasOrderings
{
    /**
     * An array of orderings that can be applied to a query.
     *
     * @return array
     */
    public function orderings();
}