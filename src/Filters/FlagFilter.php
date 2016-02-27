<?php namespace _20TRIES\Filterable\Filters;

/**
 * A filter that can be used with the Filterable trait to filter query results based on the presence
 * of a flag filter.
 *
 * @package _20TRIES\Filterable
 * @since 0.0.1
 */
class FlagFilter extends Filter
{
    /**
     * @inherit
     */
    protected $type = 'flag';

    /**
     * @inherit
     */
    protected $options = [];

    /**
     * @return string Gets the filter type; for example: "text", "select", "date_range_picker".
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
        return $this->options;
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
        return null;
    }

}