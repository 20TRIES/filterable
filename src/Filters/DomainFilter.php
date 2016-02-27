<?php namespace _20TRIES\Filterable\Filters;

/**
 * A filter that can be used with the Filterable trait to filter query results using the input
 * from a text filter that expects a domain url.
 *
 * @package _20TRIES\Filterable
 * @since 0.0.1
 */
class DomainFilter extends TextFilter
{
    /**
     * @inherit
     */
    public function validate()
    {
        return \Validator::make(['domain' => head($this->getMutatedValues())],
            [
                'domain' => 'required|url|dns',
            ],
            [
                'domain.dns' => "The DNS of the website failed validation; this usually means that the website address is invalid or that we could not get an OK response from the server."
            ]
        );
    }

    /**
     * @inherit
     */
    public function getMutatedValues()
    {
        $value = $this->values[0];

        $matches = [];

        preg_match('/(^http:\/\/|^https:\/\/)/', $value, $matches);

        if(empty($matches))
        {
            // If the domain is not prefixed with http or https we will add the "http://" prefix.
            $value = 'http://' . $value;
        }

        // Now we will remove any page information that proceeds the domain address.
        $value = preg_replace('/.*\.[^\/]*\K.*/', '', $value);

        return [$value];
    }
}