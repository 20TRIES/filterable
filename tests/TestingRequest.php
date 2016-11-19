<?php

namespace _20TRIES\Test;

use _20TRIES\Filterable\Interfaces\FilterableRequest;
use \Symfony\Component\HttpFoundation\Request;

class TestingRequest extends Request  implements FilterableRequest  {
    protected $scopes = [];

    public function __construct($query) {
        parent::__construct($query, [], [], [], [], [], '');
    }

    public function setScopes($scopes) {
        $this->scopes = $scopes;
    }

    public function scopes() {
        return $this->scopes;
    }

    public function getMethod() {
        return 'GET';
    }
}