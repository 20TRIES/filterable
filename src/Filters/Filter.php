<?php

namespace _20TRIES\Filterable\Filters;

/**
 * A filter that can be used with the Filterable trait to filter query results.
 *
 * @since 0.0.1
 */
class Filter
{
    /**
     * @var null|string The class method that a filter should be applied to.
     */
    protected $method = null;

    /**
     * @var bool A flag that indicates that the filter should be read only; if this is set then there
     *           should usually be values set that will be displayed to the user.
     */
    protected $readonly = false;

    /**
     * @var array An array of values that are set for the filter.
     */
    protected $values = [];

    /**
     * @var null|string The collection that a filter has been requested as a part of.
     */
    protected $collection = null;

    /**
     * @var null|string The name of a filter.
     */
    protected $name = null;

    /**
     * @var string The string value that should be used when grouping filters.
     */
    protected $group = 'Other';

    /**
     * Gets a function that will apply a filter to a given query.
     *
     * The function will have the following definition:
     *
     * function ($query, ...$params) : $query
     *
     * @return callable
     */
    public function getMethod()
    {
        $method = $this->method;
        return is_callable($method)
            ? $this->method
            : function ($query, ...$params) use ($method) {
                return $query->$method(...$params);
            };
    }

    /**
     * Sets the method used by a filter.
     *
     * @param string|callable $method
     * @param $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param null|string $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @return array
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function setValues(array $values)
    {
        $this->values = array_filter($values);
        return $this;
    }

    /**
     * @return bool
     */
    public function isWritable()
    {
        return $this->readonly === true;
    }

    /**
     * Creates a new filter on a model class method.
     *
     * @param $method
     *
     * @return static
     */
    public static function on($method)
    {
        $instance = new static();

        $instance->method = camel_case($method);

        $instance->name = $method;

        return $instance;
    }

    /**
     * Sets the name of a filter.
     *
     * @param string $name
     *
     * @return $this
     */
    public function withName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Sets the group for a filter.
     *
     * @param string $group
     *
     * @return $this
     */
    public function withGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Gets the filter type.
     *
     * @return string
     */
    public function getType()
    {
        return 'text';
    }

    /**
     * @return string Gets the grouping that should be used wherever the filters a grouped together; for example,
     *                when applying OptGroups in a select menu.
     */
    public function getGroup()
    {
        return $this->group;
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
     * Mutates the filter values so that it is readily prepared for processing.
     *
     * @return array
     */
    public function getMutatedValues()
    {
        return $this->getValues();
    }

    public function toArray()
    {
        return [
            // The filter type; for example: "text", "select", "date_range_picker".
            'type' => $this->getType(),

            // A flag that indicates that the filter should be read only; if this is set then there
            // should usually be values set that will be displayed to the user.
            'readonly' => $this->isWritable(),

            // A grouping that should be used wherever the filters a grouped together; for example,
            // when applying OptGroups in a select menu.
            'group' => is_null($this->getGroup()) ? 'Other' : $this->getGroup(),

            // An array of values that are set for the filter.
            'values' => $this->getValues(),

            'collection' => $this->getCollection(),
        ];
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Copies a filter.
     *
     * @return static
     */
    public function copy()
    {
        $instance = new static();

        $instance->method = $this->method;

        $instance->readonly = $this->readonly;

        $instance->collection = $this->collection;

        $instance->values = $this->values;

        $instance->name = $this->name;

        return $instance;
    }
}
