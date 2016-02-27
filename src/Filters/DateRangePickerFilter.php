<?php namespace _20TRIES\Filterable\Filters;

/**
 * A filter that can be used with the Filterable trait to filter query results using the input
 * from a date range picker.
 *
 * @package _20TRIES\Filterable
 * @since 0.0.1
 */
class DateRangePickerFilter extends Filter
{
    use FilterByDate;

    /**
     * @inherit
     */
    protected $group = 'Dates';

    /**
     * @inherit
     */
    public function getType()
    {
        return 'date_range_picker';
    }

    /**
     * @inherit
     */
    public function getOptions()
    {
        return [];
    }
}