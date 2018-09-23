<?php
namespace Ds\Interfaces;

/**
 * A sequence of unique values.
 *
 * @package Ds
 */
interface Set
{
    /**
     * Adds a value to the set.
     *
     * @param mixed $value
     */
    function add($value);

    /**
     * Determines whether the container contains a given value.
     *
     * @param mixed $value
     *
     * @return bool true if the container contains the given value, false otherwise.
     */
    function has($value): bool;

    /**
     * Removes a value from the set.
     *
     * @param mixed $value
     *
     * @return whether the value was contained in the set.
     */
    function remove($value): bool;

    /**
     * Creates a new set using values from this set that aren't in another set.
     *
     * Formally: A \ B = {x ∈ A | x ∉ B}
     *
     * @param Set $set
     *
     * @return Set
     */
    function difference(Set $set): Set;

    /**
     * Creates a new set using values in either this set or in another set,
     * but not in both. This is the symmetric difference, equivalent to XOR.
     *
     * Formally: A ⊖ B = {x : x ∈ (A \ B) ∪ (B \ A)}
     *
     * @param Set $set
     *
     * @return Set
     */
    function exclusive(Set $set): Set;

    /**
     * Creates a new set using values common to both this set and another set.
     *
     * In other words, returns a copy of this set with all values removed that
     * aren't in the other set.
     *
     * Formally: A ∩ B = {x : x ∈ A ∧ x ∈ B}
     *
     * @param Set $set
     *
     * @return Set
     */
    function intersection(Set $set): Set;

    /**
     * Creates a new set that contains the values of this set as well as the
     * values of another set.
     *
     * Formally: A ∪ B = {x: x ∈ A ∨ x ∈ B}
     *
     * @param Set $set
     *
     * @return Set
     */
    function union(Set $set): Set;
}
