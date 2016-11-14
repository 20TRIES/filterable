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
}
