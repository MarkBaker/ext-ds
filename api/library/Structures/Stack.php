<?php
namespace Ds\Structures;

use Ds\Interfaces\Stack as StackInterface;
use Ds\Interfaces\Collection;

/**
 *
 */
final class Stack implements Collection, StackInterface
{
    /**
     * @internal
     */
    private $sequence;

    /**
     *
     */
    public function __construct()
    {
        $this->sequence = new Vector();
    }

    /**
     * {@inheritDoc}
     */
    public function push($value)
    {
        $this->sequence->push($value);
    }

    /**
     * {@inheritDoc}
     */
    public function pop()
    {
        return $this->sequence->pop();
    }

    /**
     * {@inheritDoc}
     */
    public function peek()
    {
        return $this->sequence->last();
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
    public function count(): int
    {
        return count($this->sequence);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return iterator_to_array($this);
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        return $this->sequence->isEmpty();
    }
}
