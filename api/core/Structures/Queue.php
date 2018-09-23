<?php
namespace Ds\Structures;

use IteratorAggregate;

/**
 *
 */
final class Queue implements IteratorAggregate, \Ds\Interfaces\Queue
{
    /**
     * @internal
     */
    private $deque;

    /**
     *
     */
    public function __construct()
    {
        $this->deque = new Deque();
    }

    /**
     * {@inheritDoc}
     */
    public function push($value)
    {
        $this->deque->push($value);
    }

    /**
     * {@inheritDoc}
     */
    public function pop()
    {
        return $this->deque->shift();
    }

    /**
     * {@inheritDoc}
     */
    public function peek()
    {
        return $this->deque->first();
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        while ( ! $this->isEmpty()) {
            yield $this->pop();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return $this->deque->count();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return $this->deque->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }
}
