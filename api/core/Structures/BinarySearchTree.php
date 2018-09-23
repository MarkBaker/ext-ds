<?php
namespace Ds\Structures;

use Ds\Interfaces\Collection;

use function Ds\is_equal;
use Traversable;

final class BinarySearchTree implements Collection
{
    /**
     * @internal
     */
    private $array = [];

    /**
     * @internal
     */
    private $sorted = true;

    /**
     * Binary search to emulate a binary search tree. It's important
     * to use a comparison-based algorithm here so that the PHP
     * implementation can be consistent with the extension.
     *
     * @internal
     */
    private function search($value)
    {
        $this->sort();

        // Converging indices.
        $x = 0;
        $y = $this->count();

        while ($x < $y) {

            // Find the midpoint to determine a candidate.
            $m = $x / 2 + $y / 2;

            // Check to see if we have found the value.
            if (is_equal($this->array[$m], $value)) {
                return $m;
            }

            // Continue searching...
            if ($value > $this->array[$m]) {
                $x = $m + 1;
            } else {
                $y = $m - 1;
            }
        }
    }

    /**
     *
     */
    public function add($value): bool
    {
        $index = $this->search($value);

        // Can't add the same value twice.
        if (isset($index)) {
            return false;
        }

        // Add but don't sort because we might add more.
        $this->array[] = $value;
        $this->sorted  = false;

        return true;
    }

    /**
     *
     */
    public function remove($value): bool
    {
        $index = $this->search($value);
        $found = isset($index);

        // Lift the value from the array if found.
        if ($found) {
            array_splice($this->array, $index, 1);
        }

        return $found;
    }

    /**
     *
     */
    public function has($value): bool
    {
        return !is_null($this->search($value));
    }

    /**
     * @internal ensures that the internal array is sorted.
     */
    private function sort()
    {
        if ($this->sorted) {
            return;
        }

        sort($this->array);

        $this->sorted = true;
    }

    /**
     * @return array The values of this structure as an array.
     */
    public function toArray(): array
    {
        $this->sort();

        return $this->array;
    }

    /**
     * Clears or resets the collection to an initial state.
     */
    public function clear()
    {
        $this->array = [];
        $this->sorted = true;
    }

    /**
     *
     */
    public function count()
    {
        return count($this->array);
    }

    /**
     * @return bool TRUE if this collection is empty, FALSE otherwise.
     */
    public function isEmpty(): bool
    {
        return empty($this->array);
    }

    /**
     *
     */
    public function getIterator()
    {
        yield from $this->toArray();
    }
}
