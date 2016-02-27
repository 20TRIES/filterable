<?php

namespace _20TRIES\Filterable\Filters;

/**
 * A filter class that allows the filtering of results by a specific month.
 *
 * @since 0.0.1
 */
class MonthPickerFilter extends Filter
{
    use FilterByDate;

    /**
     * @inherit
     */
    protected $group = 'Dates';

    /**
     * @return string Gets the filter type; for example: "text", "select", "date_range_picker".
     */
    public function getType()
    {
        return 'month_picker';
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
}
