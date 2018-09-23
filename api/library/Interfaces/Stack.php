<?php
namespace Ds\Interfaces;

/**
 * A “last in, first out” or “LIFO” collection that only allows access to the
 * value at the top of the structure and iterates in that order, destructively.
 *
 * @package Ds
 */
interface Stack
{
    /**
     * Adds a value to the top of the stack.
     *
     * @param mixed $value
     */
    public function push($value);

    /**
     * Returns and removes the value at the top of the stack.
     *
     * @return mixed
     */
    public function pop();

    /**
     * Returns the value at the top of the stack without removing it.
     *
     * @return
     */
    public function peek();
}
