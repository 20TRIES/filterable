<?php

namespace _20TRIES\Filterable;

use _20TRIES\Filterable\Interfaces\FilterableRequest;

/**
 * A trait that includes the necessary implementation for making a controllers index action
 * filterable.
 *
 * @since 0.0.1
 */
trait Filterable
{
    /**
     * Builds a filtered query.
     *
     * @param FilterableRequest $request
     * @param mixed $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function build(FilterableRequest $request, $query) {
        return RequestToQueryAdaptor::adapt($request, $query);
    }
}
