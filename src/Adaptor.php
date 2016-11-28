<?php namespace _20TRIES\Filterable;

/**
 * Adapts user input to securely build a query.
 *
 * @package _20TRIES\Filterable\Adaptors
 */
class Adaptor
{
    /**
     * Handles the adaption of a full configuration.
     *
     * @param array $configs
     * @param array $input
     * @param mixed $query
     * @return mixed
     */
    public function adapt($configs, $input, $query)
    {
        foreach ($configs as $filter_key => $configuration) {
            $query = $this->adaptSet([$filter_key => $configuration], $input, $query);
        }
        return $query;
    }

    /**
     * Handles the adaption of a single configuration set.
     *
     * @param array $set
     * @param array $input
     * @param mixed $query
     * @return mixed
     */
    public function adaptSet($set, $input, $query)
    {
        $config = Arr::first($set, function ($value, $key) use ($input) {
            $params = array_filter(explode(',', $key));
            return !is_null($value) && (is_numeric($key) || Arr::contains($input, $params));
        });
        $method = Arr::first($config);
        $params = [];
        foreach (Arr::tail($config) as $param) {
            $params[] = $param instanceof Param ? Arr::get($input, $param->name()) : $param;
        }
        return is_callable($method) ? $method($query, ...$params) : $query;
    }
}