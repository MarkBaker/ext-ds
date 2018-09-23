<?php
namespace Ds\Interfaces;

/**
 * A Map is a sequential collection of key-value pairs, almost identical to an
 * array used in a similar context. Keys can be any type, but must be unique.
 *
 * @package Ds
 */
interface Map
{
    /**
     * Returns the value associated with a key.
     *
     * @param mixed $keyS
     *
     * @return mixed The associated value.
     *
     * @throws \RuntimeException if the key is not associated with a value.
     */
    function get($key);

    /**
     * Associates a key with a value, replacing a previous association if there
     * was one.
     *
     * @param mixed $key
     * @param mixed $value
     */
    function put($key, $value);

    /**
     * Removes a key's association from the map and returns the associated value.
     *
     * @param mixed $key
     *
     * @return mixed The associated value.
     *
     * @throws \RuntimeException if key is not associated with a value.
     */
    function remove($key);

    /**
     * Returns a set of all the keys in the map.
     *
     * @return Set
     */
    function keys(): Set;

    /**
     * Returns a sequence of all the associated values in the Map.
     *
     * @return Sequence
     */
    function values(): Sequence;
}
