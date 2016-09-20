<?php

namespace _20TRIES\Filterable;

use Illuminate\Support\Collection;

/**
 * A collection of filters.
 *
 * @since 0.0.1
 */
class FilterCollection extends Collection
{
    /**
     * @inherit
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

//    /**
//     * Calculates the difference between two collections by their keys.
//     *
//     * @param Collection $collection
//     * @return static
//     */
//    public function diffKeys(Collection $collection)
//    {
//        return $this->only($this->keys()->diff($collection->keys())->all());
//    }

    /**
     * Get the items in the collection whose keys are not present in the given items.
     *
     * @param mixed $items
     *
     * @return static
     */
    public function diffKeys($items)
    {
        return new static(array_diff_key($this->items, $this->getArrayableItems($items)));
    }

    /**
     * Returns any date filters contained within a collection of filters.
     *
     * @return static
     */
    public function dateFilters()
    {
        return $this->filter(function ($filter) {
            return $filter->getGroup() === 'Dates';
        });
    }

    /**
     * Determines whether a collection contains a date filter.
     *
     * @return bool
     */
    public function hasDateFilter()
    {
        return !is_null($this->first(function ($filter) {
            return $filter->getGroup() === 'Dates';
        }));
    }
}
