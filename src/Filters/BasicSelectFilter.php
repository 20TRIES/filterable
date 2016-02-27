<?php namespace _20TRIES\Filterable\Filters;

/**
 * A filter that can be used with the Filterable trait to filter query results using the input
 * from a select box(es).
 *
 * @package _20TRIES\Filterable
 * @since 0.0.1
 */
class BasicSelectFilter extends SelectFilter
{
    /**
     * @var array An array of possible filter value sets.
     */
    protected $options = [];

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
     * Sets the options/option sets for a filter.
     *
     * @param ...$args
     * @return $this
     */
    public function withOptions($set_name, $options)
    {
        $this->options[$set_name] = $options;

        return $this;
    }

    /**
     * @return array Mutates the filter values so that it is readily prepared for processing.
     */
    public function getMutatedValues()
    {
        $values = $this->values;

        foreach($this->values AS $key => $value)
        {
            if($value === 'null')
            {
                $values[$key] = null;
            }
        }

        return $values;
    }

    /**
     * Ensures that all values match the value of an option.
     *
     * @return array Validates the values of a filter.
     */
    public function validate()
    {
        return \Validator::make(['option' => head($this->values)],
            [
                'option' => 'required|in:' . implode(',', head($this->getOptions())),
            ],
            [
                'option.in' => 'Unrecognised filter option selected.'
            ]
        );
    }

    /**
     * Adds a "None" option to all option sets.
     */
    public function withNoneOption()
    {
        foreach($this->options AS $key => $option_set)
        {
            $this->options[$key] = array_merge(['None' => 'null'], $this->options[$key]);
        }

        return $this;
    }
}