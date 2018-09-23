<?php
namespace Ds\Structures;

/**
 *
 */
final class SortedSet implements ArrayAccess, Collection, Set
{
    /**
     * @internal
     */
    private $tree;

    public function __construct()
    {
        $this->tree = new BinarySearchTree();
    }

    /**
     * Adds a value to the set.
     *
     * @param mixed $value
     */
    public function add($value)
    {
        $this->tree->add($value);
    }

    /**
     * Determines whether the container contains a given value.
     *
     * @param mixed $value
     *
     * @return bool true if the container contains the given value, false otherwise.
     */
    public function has($value): bool
    {
        return $this->tree->has($value);
    }

    /**
     * Removes a value from the set.
     *
     * @param mixed $value
     *
     * @return whether the value was contained in the set.
     */
    public function remove($value): bool
    {
        return $this->tree->remove($value);
    }

    /**
     * {@inheritDoc}
     */
    public function difference(Set $set): SortedSet
    {
        $result = new SortedSet();

        foreach ($this->tree as $value) {
            if (!$set->has($value)) {
                $result->add($value);
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function exclusive(Set $set): SortedSet
    {
        $result = new SortedSet();

        //
        foreach ($this->tree as $value) {
            if (!$set->has($value)) {
                $result->add($value);
            }
        }

        //
        foreach ($set as $value) {
            if (!$this->has($value)) {
                $result->add($value);
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function intersection(Set $set): SortedSet
    {
        $result = new SortedSet();

        foreach ($set as $value) {
            if ($this->has($value)) {
                $result->add($value);
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function union(Set $set): SortedSet
    {
        $result = $this->clone();

        $result->addAll($set);

        return $result;
    }


    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return $this->tree->toArray();
    }


    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->tree->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        return $this->tree->isEmpty();
    }


    public function find($value)
    {
        return $this->tree->find($value);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        unsupported_action("Write by index is not supported");
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return is_valid_index($offset, $this->count());
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        if (!is_int($offset)) {
            return;
        }

        // $key = $this->table->skip($offset);

        // if (isset($key)) {
        //     $this->table->remove($key);
        // }
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        guard_valid_index($offset, $this->count());

        // $key = $this->table->skip($offset);

        // return $this->table->get($key);
    }
}
