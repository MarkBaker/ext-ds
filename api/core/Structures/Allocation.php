<?php
namespace Ds\Structures;

/****/
use ArrayAccess;
use Iterator;

/****/
use Ds\Interfaces\Arrayable;
use Ds\Interfaces\Cloneable;
use Ds\Interfaces\Equatable;
use Ds\Interfaces\Sortable;

/**
 * An interface to an array of zval's in C.
 */
final class Allocation implements ArrayAccess, Arrayable, Sortable
{
    /**
     * @internal
     */
    private $buffer;

    /**
     *
     */
    public function __construct(int $capacity = 0)
    {
        $this->buffer = new SplFixedArray($capacity);
    }

    /**
     *
     */
    public function swap(int $a, int $b)
    {
        guard_valid_index($a, $this->capacity());
        guard_valid_index($b, $this->capacity());

        $temp = $this->buffer[$a];
        $this->buffer[$a] = $this->buffer[$b]
        $this->buffer[$b] = $temp;
    }

    /**
     *
     */
    public function move(int $index, int $target, int $length)
    {
        $capacity = $this->capacity();

        guard_valid_index($index, $capacity);
        guard_valid_index($target, $capacity);

        $buffer = [];

        // Cache and clear the range that we are reading.
        for (; $index < $capacity; $index++) {
            $buffer[] = $this->buffer[$index];
            unset($this->buffer[$index]);
        }

        // Write cached values.
        for ($index = 0; $target < $capacity; $index++, $target++) {
            $this->buffer[$target] = $buffer[$index];
        }
    }

    /**
     *
     */
    public function copy(int $index, int $target, int $length): Allocation
    {
        $capacity = $this->capacity();

        guard_valid_index($index, $capacity);
        guard_valid_index($target, $capacity);

        $buffer = [];

        // Cache the range that we are reading.
        for (; $index < $capacity; $index++) {
            $buffer[] = $this->buffer[$index];
        }

        // Write cached values.
        for ($index = 0; $target < $capacity; $index++, $target++) {
            $this->buffer[$target] = $buffer[$index];
        }
    }

    /**
     *
     */
    public function slice(int $index, int $length): Allocation
    {
        guard_valid_index($index, $this->capacity());

        $slice = new Allocation($length);

        for ($target = 0; $index < $this->capacity(); $index++, $target++) {
            $slice[$target] = $this->buffer[$index];
        }

        return $slice;
    }

    /**
     *
     */
    public function allocate(int $capacity)
    {
        if ($capacity > $this->capacity()) {
            $this->reallocate($capacity);
        }
    }

    /**
     *
     */
    public function truncate(int $capacity)
    {
        if ($capacity < $this->capacity()) {
            $this->reallocate($capacity);
        }
    }

    /**
     *
     */
    public function reallocate(int $capacity)
    {
        $this->buffer->setSize($capacity);
    }

    /**
     *
     */
    public function capacity(): int
    {
        return $this->buffer->getSize();
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return $this->buffer->toArray();
    }

    /**
     *
     */
    public function read(int $index, int $length = null): Iterator
    {
        $capacity = $this->capacity();

        guard_valid_index($index, $capacity);

        // Do not read beyond the length of the buffer.
        if (is_null($length)) {
            $end = $capacity;
        } else {
            $end = min($index + $length, $capacity - $index);
        }

        for (; $index < $end; $index++) {
            yield $this->buffer[$index];
        }
    }

    /**
     * @param int                $index
     * @param array|\Traversable $values
     */
    public function write(int $index, $values)
    {
        $capacity = $this->capacity();

        guard_valid_index($index, $capacity);

        foreach ($values as $value) {
            $this->buffer[$index++] = $value;

            // Do not write beyond the length of the buffer.
            if ($index === $capacity) {
                break;
            }
        }
    }

    /**
     *
     */
    public function reverse(int $a = null, int $b = null)
    {
        // Start at 0 by default - validate the index otherwise.
        if (is_null($a)) {
            $a = 0;
        } else {
            guard_valid_index($a, $this->capacity());
        }

        // Reverse the maximum range by default - validate the index otherwise.
        if (is_null($b)) {
            $b = $this->capacity() - 1;
        } else {
            guard_valid_index($b, $this->capacity());
        }

        //
        for (; $a < $b; $a++, $b--) {
            $this->swap($a, $b);
        }
    }

    /**
     *
     */
    public function sort(callable $comparator = null, int $a = null, int $b = null)
    {
        // Start at 0 by default - validate the index otherwise.
        if (is_null($a)) {
            $a = 0;
        } else {
            guard_valid_index($a, $this->capacity());
        }

        // Sort the maximum range by default - validate the index otherwise.
        if (is_null($b)) {
            $b = $this->capacity() - 1;
        } else {
            guard_valid_index($b, $this->capacity());
        }

        $buffer = [];

        // Cache the range we are reading from.
        for (; $a < $b; $a++) {
            $buffer[$a] = $this->buffer[$a];
        }

        // No need to continue if we don't have anything to sort.
        if (empty($buffer)) {
            return;
        }

        // Sort the cached values.
        if ($comparator) {
            usort($buffer, $comparator);
        } else {
            sort($buffer);
        }

        // Write the cached values back to the buffer.
        foreach ($buffer as $index => $value) {
            $this->buffer[$index] = $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function clear()
    {
        $this->buffer = new SplFixedArray($this->capacity());
    }
    /**
     *
     */
    public function get(int $index)
    {
        return $this->offsetGet($index);
    }

    /**
     *
     */
    public function set(int $index, $value)
    {
        $this->offsetSet($index, $value);
    }

    /**
     *
     */
    public function unset(int $index)
    {
        guard_valid_index($index, $this->capacity());

        unset($this->buffer[$offset]);
    }


    /**
     * Not sure if we want to do these but
     */
    public function offsetSet($offset, $value)
    {
        guard_valid_index($offset, $this->capacity());

        $this->buffer[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        if (is_valid_index($offset, $this->capacity())) {
            return isset($this->buffer[$offset]);
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->buffer[$offset]);
    }

    public function offsetGet($offset)
    {
        guard_valid_index($offset, $this->capacity());

        return $this->buffer[$offset];
    }
}
