<?php

namespace _20TRIES\Filterable;

use _20TRIES\Filterable\Adaptors\FilteringAdaptor;
use _20TRIES\Filterable\Adaptors\OrderingAdaptor;
use Symfony\Component\HttpFoundation\Request;
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
     * Returns an array of request adaptors that should process any requests when building queries.
     *
     * @return array
     */
    protected static $available_adaptors = [
        FilteringAdaptor::class,
        OrderingAdaptor::class,
//      RelationsAdaptor::class,
//      PaginationAdaptor::class,
    ];

    /**
     * Builds a filtered query.
     *
     * @param Request $request
     * @param Model $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function build(Request $request, Model $model) {

        $query = $model->newQuery();

        foreach (self::$available_adaptors as $adaptor) {

            $adaptor = is_string($adaptor) ? new $adaptor() : $adaptor;

            if ($request instanceof $adaptor::$trait) {

                $query = $adaptor->handle($request, $query);

            }
        }

        return $query;
    }
}
