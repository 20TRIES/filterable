<?php

namespace _20TRIES\Filterable;

use _20TRIES\Filterable\Adaptors\Interfaces\FilterableRequest;
use _20TRIES\Filterable\Adaptors\RequestToQueryAdaptor;
use Illuminate\Database\Eloquent\Model;

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
     * @param Model $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function build(FilterableRequest $request, Model $model) {
        $adaptor = new RequestToQueryAdaptor();
        return $adaptor->adapt($request, $model->newQuery());
    }
}
