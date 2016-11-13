<?php

namespace _20TRIES\Test;

use _20TRIES\Filterable\Adaptors\Interfaces\FilterableRequest;
use \Symfony\Component\HttpFoundation\Request;

abstract class TestingRequest extends Request  implements FilterableRequest  {
    protected $input = [];
    public function __construct(array $query = null, array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = '') {
        $query = is_null($query) ? $this->input : $query;
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }
}