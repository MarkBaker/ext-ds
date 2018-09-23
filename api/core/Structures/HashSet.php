<?php
namespace Ds\Structures;

use ArrayAccess;

use Ds\Interfaces\Collection;
use Ds\Interfaces\Set;
use Ds\Interfaces\Sortable;

/**
 *
 */
final class HashSet implements ArrayAccess, Collection, Set, Sortable
{
    /**
     * @internal
     */
    private $table;

    public function __construct()
    {
        $this->table = new HashTable();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        $array = [];

        foreach ($this->table as $key => $value) {
            $array[] = $key;
        }

        return $array;
    }

    /**
     * {@inheritDoc}
     */
    public function sort(callable $comparator = null)
    {
        $this->table->ksort($comparator);
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->table->clear();
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        return $this->table->isEmpty();
    }

    /**
     * {@inheritDoc}
     */
    public function add($value)
    {
        $this->table->put($value, true);
    }

    /**
     * {@inheritDoc}
     */
    public function has($value): bool
    {
        return $this->table->has($value);
    }

    public function find($value)
    {
        foreach ($this as $index => $candidate) {
            if (is_equal($value, $candidate)) {
                return $index;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove($value): bool
    {
        return $this->table->remove($value);
    }

    /**
     * {@inheritDoc}
     */
    public function difference(Set $set): HashSet
    {
        $result = new HashSet();

        foreach ($this->table as $value => $_) {
            if (!$set->has($value)) {
                $result->add($value);
            }
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function exclusive(Set $set): HashSet
    {
        $result = new HashSet();

        //
        foreach ($this->table as $value => $_) {
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
    public function intersection(Set $set): HashSet
    {
        $result = new HashSet();

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
    public function union(Set $set): HashSet
    {
        $result = $this->clone();

        $result->addAll($set);

        return $result;
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

        $key = $this->table->skip($offset);

        if (isset($key)) {
            $this->table->remove($key);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        guard_valid_index($offset, $this->count());

        $key = $this->table->skip($offset);

        return $this->table->get($key);
    }
}
