<?php namespace _20TRIES\Filterable\Filters;

use _20TRIES\Filterable\Filterable;
use _20TRIES\DateRange;

/**
 * A filter that can be used with the Filterable trait to filter query results using the input
 * from a specialised combination of select boxes that allow a user to build a date range.
 *
 * @package _20TRIES\Filterable
 * @since 0.0.1
 */
class DateRangeSelectFilter extends SelectFilter
{
    use FilterByDate;

    /**
     * @inherit
     */
    protected $group = 'Dates';

    /**
     * @inherit
     */
    public function getOptions()
    {
        return [
            'operator' => [
                'In'     => 'in',
                'Not In' => 'not_in',
                'After'  => 'after',
                'Before' => 'before',
            ],
            'date_range' => [
                'Current Day'    => DateRange::today()->toInclusiveString(Filterable::$date_format, ' - '),
                'Previous Day'   => DateRange::yesterday()->toInclusiveString(Filterable::$date_format, ' - '),
                'Current Week'   => DateRange::thisWeek()->toInclusiveString(Filterable::$date_format, ' - '),
                'Last Week'      => DateRange::lastWeek()->toInclusiveString(Filterable::$date_format, ' - '),
                'Current Month'  => DateRange::thisMonth()->toInclusiveString(Filterable::$date_format, ' - '),
                'Previous Month' => DateRange::lastMonth()->toInclusiveString(Filterable::$date_format, ' - '),
                'Current Year'   => DateRange::thisYear()->toInclusiveString(Filterable::$date_format, ' - '),
                'Previous Year'  => DateRange::lastYear()->toInclusiveString(Filterable::$date_format, ' - '),
            ]
        ];
    }
}