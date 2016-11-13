<?php

namespace _20TRIES\Filterable;


use _20TRIES\Filterable\Adaptors\Interfaces\FilterableRequest;

class Param
{
    protected $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function name() {
        return $this->name;
    }

    public function getValue(FilterableRequest $request) {
        return $request->get($this->name(), null);
    }
}