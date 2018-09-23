<?php
namespace Ds\Interfaces;

/**
 * Describes the behaviour of values arranged in a single, linear dimension.
 * Some languages refer to this as a "List". It’s similar to an array that uses
 * incremental integer keys, with the exception of a few characteristics:
 *
 *  - Values will always be indexed as [0, 1, 2, …, size - 1].
 *  - Only allowed to access values by index in the range [0, size - 1].
 *
 * @package Ds
 */
interface Sequence
{
    /**
     * Returns the value at a given index (position) in the sequence.
     *
     * @param int $index
     *
     * @return mixed
     *
     * @throws \OutOfRangeException if the index is not in the range [0, size-1]
     */
    function get(int $index);

    /**
     * Removes the last value in the sequence, and returns it.
     *
     * @return mixed what was the last value in the sequence.
     *
     * @throws \UnderflowException if the sequence is empty.
     */
    function pop();

    /**
     * Adds a value to the end of the sequence.
     *
     * @param mixed $value
     */
    function push($value);

    /**
     * Removes and returns the value at a given index in the sequence.
     *
     * @param int $index this index to remove.
     *
     * @return mixed the removed value.
     *
     * @throws \OutOfRangeException if the index is not in the range [0, size-1]
     */
    function remove(int $index);

    /**
     * Replaces the value at a given index in the sequence with a new value.
     *
     * @param int   $index
     * @param mixed $value
     *
     * @throws \OutOfRangeException if the index is not in the range [0, size-1]
     */
    function set(int $index, $value);

    /**
     * Removes and returns the first value in the sequence.
     *
     * @return mixed what was the first value in the sequence.
     *
     * @throws \UnderflowException if the sequence was empty.
     */
    function shift();

    /**
     * Returns the first value in the sequence.
     *
     * @throws \UnderflowException if the sequence is empty.
     */
    function first();

    /**
     * Returns the last value in the sequence.
     *
     * @throws \UnderflowException if the sequence is empty.
     */
    function last();

    /**
     * Adds a value to the front of the sequence.
     *
     * @param mixed $value
     */
    function unshift($value);
}
