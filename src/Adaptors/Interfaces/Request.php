<?php

namespace _20TRIES\Filterable\Adaptors\Interfaces;

/**
 * A partial interface for a Symfony\Component\HttpFoundation\Request as required by Filterable components.
 *
 * @package _20TRIES\Filterable\Adaptors\Interfaces
 */
interface Request
{
    /**
     * Gets a "parameter" value from any bag.
     *
     * This method is mainly useful for libraries that want to provide some flexibility. If you don't need the
     * flexibility in controllers, it is better to explicitly get request parameters from the appropriate
     * public property instead (attributes, query, request).
     *
     * Order of precedence: PATH (routing placeholders or custom attributes), GET, BODY
     *
     * @param string $key     the key
     * @param mixed  $default the default value if the parameter key does not exist
     *
     * @return mixed
     */
    public function get($key, $default = null);
}