<?php

namespace _20TRIES\Filterable\Adaptors;

use Symfony\Component\HttpFoundation\Request;

/**
 * A request adaptor that adapts a request query to an Eloquent query.
 *
 * @package _20TRIES\Filterable\Adaptors
 */
abstract class RequestAdaptor
{
    /**
     * @var string The adaptors trait.
     */
    protected static $trait;

    /**
     * Handles the adaption.
     *
     * @param Request $request
     * @param mixed $query
     */
    public abstract function handle(Request $request, $query);
}