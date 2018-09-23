<?php
namespace Ds\Interfaces;

/**
 * A “first in, first out” or “FIFO” collection that only allows access to the
 * value at the front of the queue and iterates in that order, destructively.
 *
 * @package Ds
 */
interface Queue
{
    /**
     * Adds a value to the back of the queue.
     *
     * @param mixed $value
     */
    function push($value);

    /**
     * Returns and removes the value at the front of the queue.
     *
     * @return mixed
     */
    function pop();

    /**
     * Returns the value at the front of the queue without removing it.
     *
     * @return mixed
     */
    function peek();
}
