<?php

namespace _20TRIES\Filterable;

use _20TRIES\DateRange;
use _20TRIES\Filterable\Exceptions\FilterValidationException;
use _20TRIES\Filterable\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Input;

/**
 * A trait that includes teh necessary implementation for making a controllers index action
 * filterable.
 *
 * @since 0.0.1
 */
trait Filterable
{
    /**
     * @var array An array of filter collections, each with a name as an array key and an array of
     *            filters as the value.
     */
    protected $available_filter_collections = [];

    /**
     * @var string The name of the input attribute that should contain any requested filter_collections.
     */
    protected $filter_collection_attribute = 'filter_collections';

    /**
     * @var string The date format used by any date filters.
     */
    public static $date_format = 'd/m/Y';

    /**
     * @var string The date format deliminator used.
     */
    protected static $date_format_deliminator = '/';

    /**
     * @var array An array containing the conversions between php date format characters and the date
     *            format characters used in the js packages used.
     */
    protected static $date_format_symbol_conversions = [
        'd' => 'dd',
        'm' => 'mm',
        'Y' => 'yyyy',
    ];

    /**
     * @var array An array of suffixes that are optimised.
     */
    protected $optimized_suffixes = ['_in', '_between'];

    /**
     * @var array An array of data describing the filters that this controller can accept when
     *            retrieving records via the index method.
     */
    protected $available_filters;

    /**
     * @var array An array of parsed filters that have been input by the user.
     */
    protected $active_filters = [];

    /**
     * @var array An array of active filter collections.
     */
    protected $active_collections = [];

    /**
     * @var array An array of relations that are loaded by the class.
     */
    protected $load = [];

    /**
     * @var array The number of results that should be returned.
     */
    protected $limit = 15;

    /**
     * @var array The maximum number of results that can be returned.
     */
    protected $limit_min = 0;

    /**
     * @var array The minimum number of results that can be returned.
     */
    protected $limit_max = 100;

//    protected $order_by = 0; // @TODO Add support for option
//
//    protected $order_direction = 'asc'; // @TODO Add support for option
//
//    protected $fields = []; // @TODO Add support for option

    /**
     * Registers a group of filters.
     *
     * @param array ...$args
     *
     * @return $this
     */
    public function registerFilters(...$args)
    {
        $this->available_filters = (new FilterCollection($args))->keyBy(function ($item) {
            return $item->getName();
        });

        return $this;
    }

    /**
     * @return array An array of relationships that should be loaded when building a query.
     */
    public function shouldLoad()
    {
        return $this->load;
    }

    /**
     * Gets the number of results that should be returned by any query.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Gets the minimum number of results that a query can return.
     *
     * @return int
     */
    public function getLimitMin()
    {
        return $this->limit_min;
    }

    /**
     * Gets the maximum number of results that a query can return.
     *
     * @return int
     */
    public function getLimitMax()
    {
        return $this->limit_max;
    }

    /**
     * Gets the filters from the current requests and performs and parsing required.
     *
     * @param array $options
     */
    public function initialiseFilters($options = [])
    {
        $resolve_input = Arr::get($options, 'resolve_input', true);

        // Process collections
        $this->setAvailableCollections();

        $collections = Arr::get($options, 'collections', []);

        if ($resolve_input === true) {
            $collections = array_merge($this->resolveCollections(), $collections);
        }

        $this->parseFilterCollections($collections);

        // Process Filters
        $filters = Arr::get($options, 'filters', []);

        if ($resolve_input === true) {
            $filters = array_merge($this->resolveFilters(), $filters);
        }

        foreach ($filters as $item => $value) {
            if (isset($this->available_filters[$item])) {
                $filter = &$this->available_filters[$item];

                $filter->setValues(is_array($value) ? $value : [$value]);

                // First we will handle any validation required.
                $this->validateFilter($filter);

                // Now we can store the filter
                $this->activateFilter($item, $filter);
            }
        }

        // Process Loads
        $this->load = Arr::get($options, 'load', []);

        if ($resolve_input === true) {
            $this->load = array_merge($this->resolveLoads(), $collections);
        }

        // Setup Pagination
        $this->setupPagination(array_only($options, ['limit', 'limit_min', 'limit_max']));

        // Share properties
        $share_properties = Arr::get($options, 'share_properties', true);

        if ($share_properties == true) {
            $this->registerSharedVariables();
        }
    }

    /**
     * Gets the input for the current request.
     *
     * @return array
     */
    protected function getInput()
    {
        return Input::all();
    }

    /**
     * Resolves any collections requested via input in the current request.
     *
     * @return array
     */
    protected function resolveCollections()
    {
        return Arr::get($this->getInput(), 'collections', []);
    }

    /**
     * Resolves any filters requested via input in the current request.
     *
     * @return array
     */
    protected function resolveFilters()
    {
        return Arr::get($this->getInput(), 'filters', []);
    }

    /**
     * Resolves any relations requested to be loaded via input in the current request.
     *
     * @return array
     */
    protected function resolveLoads()
    {
        return Arr::get($this->getInput(), 'load', []);
    }

    /**
     * Setup pagination.
     *
     * @param $options
     */
    protected function setupPagination($options)
    {
        $this->limit = Arr::get($options, 'limit', 15);
        $this->limit_min = Arr::get($options, 'limit_min', 0);
        $this->limit_max = Arr::get($options, 'limit_max', 100);
    }

    /**
     * Builds the query from the active filters.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function buildQuery($query)
    {
        $filters = $this->optimizeFilters();

        // Process each filter
        foreach ($filters as $name => $filter) {
            $method = $filter->getMethod();

            $args = $filter->getMutatedValues();

            $query = $query->$method(...$args);
        }

        // Apply pagination
        $limit = $this->getLimit();
        $limit = $limit > $this->getLimitMax() ? $this->getLimitMax() : $limit;
        $limit = $limit < $this->getLimitMin() ? $this->getLimitMin() : $limit;

        $query->simplePaginate($limit);

        $query->appends(array_except($this->getInput(), ['page']));

        // Load requested relations
        if (!empty($this->load)) {
            $query = $query->with($this->load);
        }

        return $query;
    }

    /**
     * Registers any variables that should be shared with the filterable views.
     */
    public function registerSharedVariables()
    {
        app('view')->share('date_filter', $this->getDateFilter());
        app('view')->share('inactive_filters', $this->getInactiveFilters());
        app('view')->share('filters', $this->getActiveFilters());
        app('view')->share('available_filters', $this->getAvailableFilters());
        app('view')->share('active_filter_collections', $this->getActiveCollections());
        app('view')->share('time_periods', DateRange::$time_periods);
    }

    /**
     * Optimizes the active filters using the the $optimized_suffixes attribute.
     *
     * @return array
     */
    protected function optimizeFilters()
    {
        $remove = [];

        foreach ($this->active_filters as $filter => $data) {
            foreach ($this->optimized_suffixes as $suffix) {
                if (isset($this->active_filters[$filter.$suffix])) {
                    $remove[] = $filter;
                }
            }
        }

        return array_except($this->active_filters, $remove);
    }

    /**
     * Handles the validation of a raw filter.
     *
     * @param array $filter
     *
     * @throws FilterValidationException
     */
    protected function validateFilter($filter)
    {
        $validator = $filter->validate();

        if (!is_null($validator) && $validator->fails()) {
            if (request()->acceptsHtml()) {
                $response = redirect()->back()->withErrors($validator, 'filterable');

                \Session::save();

                $response->send();
            } else {
                (new JsonResponse($validator->errors()->all(), 422))->send();
            }
        }
    }

    /**
     * Gets the active filters that have been requested by the user and parsed.
     *
     * @return array
     */
    protected function getActiveFilters()
    {
        return new FilterCollection($this->active_filters);
    }

    /**
     * Activates a collection of filters.
     *
     * @param $filters
     */
    protected function activateFilters($filters)
    {
        foreach ($filters as $filter => $data) {
            $this->activateFilter($filter, $data);
        }
    }

    /**
     * Activate a filter.
     *
     * @param string $filter_name
     * @param array  $filter_data
     */
    protected function activateFilter($filter_name, $filter_data)
    {
        if (!$this->available_filters->has($filter_name)) {
            throw new \InvalidArgumentException("Unavailable filter:  $filter_name!");
        }

        if ($this->getActiveFilters()->has($filter_name)) {
            $filter = $this->active_filters[$filter_name];
        } else {
            $filter = $this->available_filters[$filter_name];
        }

        foreach ($filter_data as $key => $value) {
            $set = 'set'.ucfirst(camel_case($key));

            $filter->$set($value);
        }

        $this->active_filters[$filter_name] = $filter;
    }

    /**
     * Gets the available filters that are not active.
     *
     * @return array
     */
    protected function getInactiveFilters()
    {
        return $this->available_filters->diffKeys($this->getActiveFilters());
    }

    /**
     * Gets all available filters.
     *
     * @return array
     */
    protected function getAvailableFilters()
    {
        return $this->available_filters->merge($this->getActiveFilters());
    }

    /**
     * Gets the date filter that is being applied to a query.
     *
     * @return Filter|null
     */
    protected function getDateFilter()
    {
        // Now we will look for a date filter and if one is set, will pass this, with its values mutated
        // to the view.
        $date_filter = collect($this->getActiveFilters())->first(function ($name, $filter) {
            return $filter->getGroup() == 'Dates';
        });

        return is_null($date_filter) ? null : $date_filter->copy()->mutate();
    }

    /**
     * Gets the date format parsed specifically for the Date Range Picker package used.
     *
     * @return string
     */
    public static function getDateFormatForRangePicker()
    {
        $components = explode(static::$date_format_deliminator, static::$date_format);

        $conversions = static::$date_format_symbol_conversions;

        foreach ($components as $key => $component) {
            if (array_key_exists($component, $conversions)) {
                $components[$key] = $conversions[$component];
            }
        }

        return implode(static::$date_format_deliminator, $components);
    }

    /**
     * Gets the available filter collections.
     *
     * @return array
     */
    protected function getAvailableCollections()
    {
        return $this->available_filter_collections;
    }

    /**
     * Sets the $filter_collections attribute.
     */
    protected function setAvailableCollections()
    {
        $this->available_filter_collections = [];
    }

    /**
     * Sets the $filter_collections attribute.
     */
    protected function getActiveCollections()
    {
        return $this->active_collections;
    }

    /**
     * Parses collections.
     *
     * @param $collections
     */
    protected function parseFilterCollections($collections)
    {
        $collections = array_intersect_key($this->getAvailableCollections(), array_flip($collections));

        $this->active_collections = array_unique(array_filter($collections));

        foreach ($this->active_collections as $collection => $filters) {
            $parsed_filters = [];

            foreach ($filters as $key => $data) {
                if (is_numeric($key)) {
                    $parsed_filters[$data] = ['collection' => $collection];
                } else {
                    $parsed_filters[$key] = array_merge($data, ['collection' => $collection]);
                }
            }

            $this->activateFilters($parsed_filters);
        }
    }
}
