<?php
namespace Ds\Interfaces;

/**
 * Indicates that a structure can be sorted in-place.
 */
interface Sortable
{
    /**
     * Sorts the structure in-place, based on an optional callable comparator.
     *
     * @param callable|null $comparator Accepts two values to be compared.
     *                                  Should return the result of a <=> b.
     */
    function sort(callable $comparator = null);
}
