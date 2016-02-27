<?php

namespace _20TRIES\Filterable\Filters;

/**
 * A filter that can be used with the Filterable trait to filter query results using the input
 * from a / multiple select boxes.
 *
 * @since 0.0.1
 */
abstract class SelectFilter extends Filter
{
    /**
     * @inherit
     */
    protected $type = 'select';

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inherit
     */
    public function toArray()
    {
        $data = parent::toArray();

        if (!empty($this->getOptions()) && $this->type === 'select') {
            $options = [];

            foreach ($this->getOptions() as $key => $value) {
                $options[] = [$key => $value];
            }
        }

        // Used when the filter type is "select" and therefore there are options that need to be
        // presented to the user.
        $data['options'] = isset($options) ? $options : $this->getOptions();

        return $data;
    }
}
