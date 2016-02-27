<?php

namespace _20TRIES\Filterable\Filters;

class DateFilter extends Filter
{
    /**
     * @return string Gets the filter type; for example: "text", "select", "date_range_picker".
     */
    public function getType()
    {
        // TODO: Implement getType() method.
    }

    /**
     * Gets options that need to be presented to the user.
     *
     * @return array
     */
    public function getOptions()
    {
        // TODO: Implement getOptions() method.
    }

    /**
     * @return array Mutates the filter values so that it is readily prepared for processing.
     */
    public function getMutatedValues()
    {
        $operator = head($this->values);

        $dates = explode(' - ', last($this->values));

        switch ($operator) {
            case 'before':
                return [DateRange::before(Carbon::createFromFormat(Filterable::$date_format, head($dates), 'GB')->startOfDay())];
            case 'in':
                return [new DateRange(
                    Carbon::createFromFormat(Filterable::$date_format, head($dates), 'GB')->subDay()->endOfDay(),
                    Carbon::createFromFormat(Filterable::$date_format, last($dates), 'GB')->addDay()->startOfDay()
                )];
            case 'not_in':
                return [new DateRange(
                    Carbon::createFromFormat(Filterable::$date_format, last($dates), 'GB')->addDay()->startOfDay(),
                    Carbon::createFromFormat(Filterable::$date_format, head($dates), 'GB')->subDay()->endOfDay()
                )];
            case 'after':
                return [DateRange::after(Carbon::createFromFormat(Filterable::$date_format, last($dates), 'GB')->endOfDay())];
        }
    }

    /**
     * @return \Validator Returns a validator for the filter.
     */
    public function validate()
    {
        return \Validator::make(['operator' => head($this->values), 'date' => last($this->values)],
            [
                'operator' => 'required|in:before,in,not_in,after',
                'date'     => 'required|date_format:d/m/Y - d/m/Y',
            ]
        );
    }
}
