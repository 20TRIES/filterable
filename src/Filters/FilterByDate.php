<?php namespace _20TRIES\Filterable\Filters;

use _20TRIES\Filterable\Filterable;
use _20TRIES\DateRange;
use Carbon\Carbon;

/**
 * Trait that enables filtering by dates.
 *
 * @package _20TRIES\Filterable
 * @since 0.0.1
 */
trait FilterByDate
{
    /**
     * @return array Mutates the filter values so that it is readily prepared for processing.
     * @throws \Exception
     */
    public function getMutatedValues()
    {
        $operator = head($this->values);

        $dates = explode(' - ', last($this->values));

        switch ($operator)
        {
            case "before":
                $values = [DateRange::before(Carbon::createFromFormat(Filterable::$date_format, head($dates), 'GB')->startOfDay())];
                break;
            case "not_in":
                $values = [new DateRange(
                    Carbon::createFromFormat(Filterable::$date_format, last($dates), 'GB')->addDay()->startOfDay(),
                    Carbon::createFromFormat(Filterable::$date_format, head($dates), 'GB')->subDay()->endOfDay()
                )];
                break;
            case "after":
                $values = [DateRange::after(Carbon::createFromFormat(Filterable::$date_format, last($dates), 'GB')->endOfDay())];
                break;
            case "in":
            default:
                $values = [new DateRange(
                    Carbon::createFromFormat(Filterable::$date_format, head($dates), 'GB')->subDay()->endOfDay(),
                    Carbon::createFromFormat(Filterable::$date_format, last($dates), 'GB')->addDay()->startOfDay()
                )];
        }

        return $values;
    }

    /**
     * @return \Validator Returns a validator for the filter.
     */
    public function validate()
    {
        $data['date'] = last($this->values);

        if(count($this->values) > 1)
        {
            $data['operator'] = head($this->values);
        }

        $rules = [
            'operator' => 'in:before,in,not_in,after',
            'date'     => 'required|date_format:d/m/Y - d/m/Y',
        ];

        return \Validator::make($data, $rules);
    }
}