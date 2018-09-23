<?php
namespace Ds\Structures;

use ArrayAccess;
use IteratorAggregate;

use Ds\Exceptions\EmptyStateException;
use Ds\Interfaces\Collection;
use Ds\Interfaces\Sequence;
use Ds\Interfaces\Sortable;

use function Ds\guard_not_empty;
use function Ds\guard_valid_index;
use function Ds\filter_iterator;
use function Ds\map_iterator;
use function Ds\normalize_slice_params;
use function Ds\reduce;

/**
 * @todo we should benchmark this against PHP DS 1.0
 */
final class Vector implements ArrayAccess, Collection, Sequence, Sortable
{
    /**
     * Memory allocation
     */
    private $alloc;

    /**
     * Number of items in the vector.
     */
    private $count;

    /**
     * Memory allocation growth multiplier.
     */
    const GROWTH_FACTOR = 1.5;

    /**
     * Starting capacity, which can be 0.
     */
    const START_CAPACITY = 0;

    /**
     * Lowest allowed non-zero capacity.
     */
    const MIN_CAPACITY = 8;

    /**
     *
     */
    public function __construct()
    {
        $this->alloc = new Allocation(Vector::START_CAPACITY);
        $this->count = 0;
    }

    /**
     * If we don't have enough capacity, we need to allocate more.
     *
     * We want to make sure that we allocate enough for other values
     * as well to prevent allocating too frequently.
     *
     * The resulting capacity is the greater between the required
     * capacity and the next default growth capacity of this vector.
     */
    private function ensureCapacityFor(int $required)
    {
        $capacity = $this->alloc->capacity();

        if ($capacity < $required) {
            $capacity = max(
                $required,
                Vector::MIN_CAPACITY,
                Vector::GROWTH_FACTOR * $capacity);

            $this->alloc->allocate($capacity);
        }
    }

    /**
     *
     */
    private function checkAutoTruncation()
    {
        if ($this->count < $this->alloc->capacity() / 4) {
            $this->alloc->truncate($this->alloc->capacity() / 2);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function sort(callable $comparator = null)
    {
        $this->alloc->sort($comparator, 0, $this->count);
    }

    /**
     * {@inheritDoc}
     */
    public function equals($other): bool
    {
        if ($other instanceof Vector && $this->count === $other->count) {
            return $this->alloc->equals($other->alloc, 0 $this->count);
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function get(int $index)
    {
        guard_valid_index($index, 0, $this->count - 1);

        return $this->alloc->get($index);
    }

    /**
     * {@inheritDoc}
     */
    public function pop()
    {
        guard_not_empty($this);

        $value = $this->alloc->get($this->count - 1);

        $this->count--;
        $this->checkAutoTruncation();

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function push($value)
    {
        $this->ensureCapacityFor($this->count + 1);
        $this->alloc->set($this->count, $value);
        $this->count++;
    }

    /**
     *
     */
    public function pushAll($values)
    {
        guard_is_traversable($values);

        $count = silent_count($values);

        // @todo benchmark this
        if (!is_null($count)) {
            $this->ensureCapacityFor($this->count + $length);
            $this->alloc->write($this->count, $values);
            $this->count += $length;
        } else {
            foreach ($values as $value) {
                $this->push($value);
            }
        }
    }

    /**
     * Inserts a value at a given index.
     *
     * Each value after the index will be moved one position to the right.
     *
     * Values may be inserted at an index equal to the size of the sequence.
     *
     * @param int   $index
     * @param mixed $value
     *
     * @throws \OutOfRangeException if the index is not in the range [0, size]
     */
    public function insert(int $index, $value)
    {
        guard_valid_index($index, 0, $this->count);

        $this->ensureCapacityFor($this->count + 1);

        $this->alloc->move($index, $index + 1, $this->count - $index);
        $this->alloc->set($index, $value);

        $this->count++;
    }

    /**
     *
     */
    public function insertAll(int $index, $values)
    {
        guard_valid_index($index, 0, $this->count);
        guard_is_traversable($values);

        $length = silent_count($values);

        // @todo benchmark this.
        if (!is_null($length)) {
            $this->ensureCapacityFor($this->count + $length);
            $this->alloc->move($index, $index + $length, $length);
            $this->alloc->write($index, $values);
            $this->count += $length;
        } else {
            foreach ($values as $value) {
                $this->insert($index++, $value);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove(int $index)
    {
        guard_valid_index($index, 0, $this->count - 1);

        $value = $this->alloc->unset($index);

        // Move all values after the index one position to the front.
        $this->alloc->move($index + 1, $index, $this->count - $index - 1);
        $this->count--;

        $this->checkAutoTruncation();

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function set(int $index, $value)
    {
        guard_valid_index($index, 0, $this->count - 1);

        $this->alloc->set($index, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function shift()
    {
        guard_not_empty($this);

        return $this->remove(0);
    }

    /**
     * {@inheritDoc}
     */
    public function unshift($value)
    {
        $this->ensureCapacityFor($this->count + 1);

        //
        $this->alloc->move(0, 1, $this->count);
        $this->alloc->set(0, $value);

        $this->count++;
    }

    /**
     *
     */
    public function unshiftAll($values)
    {
        guard_is_traversable($values);

        $length = silent_count($values);

        if (!is_null($length)) {
            $this->ensureCapacityFor($this->count + $length);
            $this->alloc->move(0, $length, $this->count);
            $this->alloc->write(0, $values);
            $this->count += $length;
        } else {
            $count = $this->count();

            foreach ($values as $value) {
                $this->unshift($value);
            }
            // We need to reverse the unshifted values to preserve ordering.
            $this->alloc->reverse(0, ($this->count() - $count) - 1);
        }

    }

    /**
     * Returns a new sequence containing only the values for which a callback
     * returns true. A boolean test will be used if a callback is not provided.
     *
     * @param callable|null $callback Accepts a value, returns a boolean result:
     *                                true : include the value,
     *                                false: skip the value.
     *
     * @return Vector
     */
    public function filter(callable $predicate = null): Vector
    {
        // @todo we should benchmark this against creating a vector directly, for
        // cases where the filtered count is 0, n/4, n/2, 3n/4, n.
        $alloc = new Allocation($this->count);
        $count = 0;

        foreach (filter_iterator($this, $predicate) as $value) {
            $alloc->set($count++, $value);
        }

        // Truncate to the number of included items.
        $alloc->truncate($count);

        // Create a new vector using the allocation.
        $vector = new Vector();
        $vector->count = $count;
        $vector->alloc = $alloc;

        return $vector;
    }

    /**
     * Returns a new sequence using the results of applying a callback to each
     * value.
     *
     * @param callable $callback
     *
     * @return Vector
     */
    public function map(callable $callback): Vector
    {
        return map_by_reference($this->clone(), $callback);
    }

    /**
     * Returns a sub-sequence of a given length starting at a specified index.
     *
     * @param int $index  If the index is positive, the sequence will start
     *                    at that index in the sequence. If index is negative,
     *                    the sequence will start that far from the end.
     *
     * @param int $length If a length is given and is positive, the resulting
     *                    sequence will have up to that many values in it.
     *                    If the length results in an overflow, only values
     *                    up to the end of the sequence will be included.
     *
     *                    If a length is given and is negative, the sequence
     *                    will stop that many values from the end.
     *
     *                    If a length is not provided, the resulting sequence
     *                    will contain all values between the index and the
     *                    end of the sequence.
     *
     * @return Vector
     */
    public function slice(int $index, int $length = null): Vector
    {
        if (is_null($length)) {
            $length = $this->count;
        }

        normalize_slice_params($index, $length, $this->count);

        $slice = new Vector();
        $slice->alloc = $this->alloc->copy($index, $length);
        $slice->count = $length;

        return $slice;
    }

    /**
     *
     */
    public function reverse()
    {
        $this->alloc->reverse(0, $this->count);
    }

    /**
     * Iteratively reduces the sequence to a single value using a callback.
     *
     * @param callable $callback Accepts the carry and current value, and
     *                           returns an updated carry value.
     *
     * @param mixed $initial Optional initial carry value.
     *
     * @return mixed The carry value of the final iteration, or the initial
     *               value if the sequence was empty.
     */
    public function reduce(callable $callback, $initial = null)
    {
        return reduce($this, $callback, $initial);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->push($value);
        } else {
            $this->set($offset, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        if ($offset < 0 || $offset >= $this->count) {
            return false;
        }

        return isset($this->get($offset));
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        if ($offset >= 0 && $offset < $this->count) {
            $this->remove($offset);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
}
