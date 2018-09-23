<?php
namespace Ds\Structures;

/****/
use LogicException;

/****/
use Ds\Interfaces\Collection;

/**
 * @todo lookups are O(n) at the moment, rather than O(1)
 */
final class HashTable implements Collection, Sortable
{
    /**
     * @internal
     */
    private $array;

    /**
     * Sorts the table in-place by value, using an optional callable comparator.
     *
     * @param callable|null $comparator Accepts two values to be compared.
     *                                  Should return the result of a <=> b.
     */
    public function sort(callable $comparator = null)
    {
        if ($comparator) {
            usort($this->array, $comparator);
        } else {
            sort($this->array);
        }
    }

    /**
     * Sorts the table in-place by key, using an optional callable comparator.
     *
     * @param callable|null $comparator Accepts two keys to be compared.
     *                                  Should return the result of a <=> b.
     */
    public function ksort(callable $comparator = null)
    {
        if ($comparator) {
            uksort($this->array, $comparator);
        } else {
            ksort($this->array);
        }
    }

    /**
     *
     */
    public function clear()
    {
        $this->array = [];
    }

    /**
     * Returns the value associated with a key.
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed The associated value.
     *
     * @throws \LogicException if the key is not associated with a value.
     */
    public function get($key)
    {
        if (!is_null($key)) {
            foreach ($this->array as $pair) {
                if (is_equal($key, $pair[0])) {
                    return $value;
                }
            }
        }

        /**
         * Caller should know that the key exists, eg. using `has` before `get`.
         * We can not return NULL or FALSE here because it would be ambiguous.
         */
        throw new LogicException("Key not found");
    }

    /**
     * @return The key at a given position from the front of the table.
     */
    public function skip(int $index)
    {
        guard_valid_index($index, 0, $this->count() - 1);

        return $this->array[$index][0];
    }

    /**
     * @param mixed $key The key to look for in the table.
     *
     * @return bool TRUE if the key is in the table, FALSE otherwise.
     */
    public function has($key): bool
    {
        if (!is_null($key)) {
            foreach ($this->array as $pair) {
                if (is_equal($key, $pair[0])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param mixed $value The value for which to find an associated key.
     *
     * @return mixed The first key associated with the given value, or NULL if
     *               the value is not associated with a key in this table.
     */
    public function find($value)
    {
        foreach ($this->array as $pair) {
            if (is_equal($value, $pair[1])) {
                return $pair[0];
            }
        }

        return null;
    }

    /**
     * Associates a given key with a given value. Any value that is already
     * associated with the given key will be replaced.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function put($key, $value)
    {
        if (is_null($key)) {
            throw new InvalidArgumentException("Key may not be NULL");
        }

        foreach ($this->array as $pair) {
            if (is_equal($key, $pair[0])) {
                $pair[1] = $value;
            }
        }

        $this->array[] = [$key, $value];
    }

    /**
     * Removes a given key's association from the table.
     *
     * @return bool TRUE if the key was removed, FALSE if not found.
     */
    public function remove($key): bool
    {
        if (!is_null($key)) {
            foreach ($this->array as $index => $pair) {
                if (is_equal($key, $pair[0])) {
                    unset($this->array[$index]);
                    return true;
                }
            }
        }

        return false;
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
    public function toArray(): array
    {
        return $this->array;
    }

    /**
     * {@inheritDoc}
     */
    public function isEmpty(): bool
    {
        return count($this->array) === 0;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        foreach ($this->array as $pair) {
            yield $pair[0] => $pair[1];
        }
    }
}
