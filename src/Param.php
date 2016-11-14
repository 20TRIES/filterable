<?php

namespace _20TRIES\Filterable;

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
}