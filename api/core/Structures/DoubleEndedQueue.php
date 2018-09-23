<?php
namespace Ds\Structures;

use IteratorAggregate;

use Ds\Interfaces\Collection;
use Ds\Interfaces\Sequence;

/**
 *
 */
final class DoubleEndedQueue implements Collection
{
    /**
     * @internal
     */
    private $array = [];

    /**
     *
     */
    public function push($value)
    {
        array_push($this->array, $value);
    }

    /**
     *
     */
    public function unshift($value)
    {
        array_unshift($this->array, $value);
    }

    /**
     * @throws \LogicException if empty.
     */
    public function pop()
    {
        if ($this->isEmpty()) {
            throw new \LogicException("Unexpected empty state");
        }

        return array_pop($this->array);
    }

    /**
     *
     */
    public function shift()
    {
        if ($this->isEmpty()) {
            throw new \LogicException("Unexpected empty state");
        }

        return array_shift($this->array);
    }

    /**
     * @throws \LogicException if empty.
     */
    public function first()
    {
        if ($this->isEmpty()) {
            throw new \LogicException("Unexpected empty state");
        }

        return $this->array[0];
    }

    /**
     * @throws \LogicException if empty.
     */
    public function last()
    {
        if ($this->isEmpty()) {
            throw new \LogicException("Unexpected empty state");
        }

        return $this->array[count($this->array) - 1];
    }

    /**
     * {@inheritDoc}
     */
    public function count(): int
    {
        return count($this->array);
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return $this->array;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return $this->array;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        return empty($this->array);
    }

    /**
     * Clears or resets the collection to an initial state.
     */
    function clear()
    {
        $this->array = [];
    }
}
