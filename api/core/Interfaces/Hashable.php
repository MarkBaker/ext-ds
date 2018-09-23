<?php
namespace Ds\Interfaces;

/**
 * Indicates that an object is designed to be used as a key in a hash table.
 */
interface Hashable
{
    /**
     * @return mixed The value to be hashed by the internal hashing algorithm.
     */
    function hashCode();
}
