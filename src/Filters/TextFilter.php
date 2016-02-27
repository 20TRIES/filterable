<?php

namespace _20TRIES\Filterable\Filters;

/**
 * A filter that can be used with the Filterable trait to filter query results with a text input
 * from the user.
 *
 * @since 0.0.1
 */
class TextFilter extends Filter
{
    protected $type = 'text';

    /**
     * @inherit
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets options that need to be presented to the user.
     *
     * @return array
     */
    public function getOptions()
    {
        return [];
    }

    /**
     * @return array Mutates the filter values so that it is readily prepared for processing.
     */
    public function getMutatedValues()
    {
        return $this->values;
    }

    /**
     * @return array Validates the values of a filter.
     */
    public function validate()
    {
        return;
    }
}
