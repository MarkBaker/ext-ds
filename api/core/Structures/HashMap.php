<?php
namespace Ds\Structures;

use ArrayAccess;

use Ds\Interfaces\Collection;
use Ds\Interfaces\Map;
use Ds\Interfaces\Sortable;

/*
<?php
namespace Ds\Structures;

// /****/
// use LogicException;

// /****/
// use Ds\Interfaces\Collection;

// /**
//  * @todo lookups are O(n) at the moment, rather than O(1)
//  */
// final class HashTable implements Collection, Sortable
// {
//     /**
//      * @internal
//      */
//     private $array;

//     /**
//      * Sorts the table in-place by value, using an optional callable comparator.
//      *
//      * @param callable|null $comparator Accepts two values to be compared.
//      *                                  Should return the result of a <=> b.
//      */
//     public function sort(callable $comparator = null)
//     {
//         if ($comparator) {
//             usort($this->array, $comparator);
//         } else {
//             sort($this->array);
//         }
//     }

//     /**
//      * Sorts the table in-place by key, using an optional callable comparator.
//      *
//      * @param callable|null $comparator Accepts two keys to be compared.
//      *                                  Should return the result of a <=> b.
//      */
//     public function ksort(callable $comparator = null)
//     {
//         if ($comparator) {
//             uksort($this->array, $comparator);
//         } else {
//             ksort($this->array);
//         }
//     }

//     /**
//      *
//      */
//     public function clear()
//     {
//         $this->array = [];
//     }

//     /**
//      * Returns the value associated with a key.
//      *
//      * @param mixed $key
//      * @param mixed $default
//      *
//      * @return mixed The associated value.
//      *
//      * @throws \LogicException if the key is not associated with a value.
//      */
//     public function get($key)
//     {
//         if (!is_null($key)) {
//             foreach ($this->array as $pair) {
//                 if (is_equal($key, $pair[0])) {
//                     return $value;
//                 }
//             }
//         }

//         /**
//          * Caller should know that the key exists, eg. using `has` before `get`.
//          * We can not return NULL or FALSE here because it would be ambiguous.
//          */
//         throw new LogicException("Key not found");
//     }

//     /**
//      * @return The key at a given position from the front of the table.
//      */
//     public function skip(int $index)
//     {
//         guard_valid_index($index, 0, $this->count() - 1);

//         return $this->array[$index][0];
//     }

//     *
//      * @param mixed $key The key to look for in the table.
//      *
//      * @return bool TRUE if the key is in the table, FALSE otherwise.

//     public function has($key): bool
//     {
//         if (!is_null($key)) {
//             foreach ($this->array as $pair) {
//                 if (is_equal($key, $pair[0])) {
//                     return true;
//                 }
//             }
//         }

//         return false;
//     }

//     /**
//      * @param mixed $value The value for which to find an associated key.
//      *
//      * @return mixed The first key associated with the given value, or NULL if
//      *               the value is not associated with a key in this table.
//      */
//     public function find($value)
//     {
//         foreach ($this->array as $pair) {
//             if (is_equal($value, $pair[1])) {
//                 return $pair[0];
//             }
//         }

//         return null;
//     }

//     /**
//      * Associates a given key with a given value. Any value that is already
//      * associated with the given key will be replaced.
//      *
//      * @param mixed $key
//      * @param mixed $value
//      *
//      * @return void
//      */
//     public function put($key, $value)
//     {
//         if (is_null($key)) {
//             throw new InvalidArgumentException("Key may not be NULL");
//         }

//         foreach ($this->array as $pair) {
//             if (is_equal($key, $pair[0])) {
//                 $pair[1] = $value;
//             }
//         }

//         $this->array[] = [$key, $value];
//     }

//     /**
//      * Removes a given key's association from the table.
//      *
//      * @return bool TRUE if the key was removed, FALSE if not found.
//      */
//     public function remove($key): bool
//     {
//         if (!is_null($key)) {
//             foreach ($this->array as $index => $pair) {
//                 if (is_equal($key, $pair[0])) {
//                     unset($this->array[$index]);
//                     return true;
//                 }
//             }
//         }

//         return false;
//     }

//     /**
//      * {@inheritDoc}
//      */
//     public function count(): int
//     {
//         return count($this->array);
//     }

//     /**
//      * {@inheritDoc}
//      */
//     public function toArray(): array
//     {
//         return $this->array;
//     }

//     /**
//      * {@inheritDoc}
//      */
//     public function isEmpty(): bool
//     {
//         return count($this->array) === 0;
//     }

//     /**
//      * {@inheritDoc}
//      */
//     public function getIterator()
//     {
//         foreach ($this->array as $pair) {
//             yield $pair[0] => $pair[1];
//         }
//     }
// }

/**
 *
 */
final class HashMap implements ArrayAccess, Collection, Map, Sortable
{
    /**
     * @internal
     */
    private $table;

    public function __construct()
    {
        $this->table = new HashTable();
    }

    public function first()
    {
        return $this->table->skip(0);
    }

    public function last()
    {
        return $this->table->skip($this->count() - 1);
    }

    public function union(Map $map): HashMap
    {
        $result = $this->clone();

        $result->putAll($map);

        return $result;
    }

    public function intersection(Map $map): HashMap
    {
        $result = new HashMap();

        foreach ($map as $key => $value) {
            if ($this->has($key)) {
                $result->put($key, $value);
            }
        }

        return $result;
    }

    public function difference(Map $map): HashMap
    {
        $result = $this->clone();

        foreach ($map as $key => $value) {
            $result->remove($key); // REMOVE MUST BE SILENT, OR DEFAULTED
        }

        return $result;
    }

    public function exclusive(Map $map): HashMap
    {
        $result = $this->clone();

        foreach ($map as $key => $value) {
            $found = $result->remove($key);

            //
            if (!$found) {
                $result->add($key);
            }
        }

        return $result;
    }

    public function filter(callable $predicate = null): HashMap
    {
        $result = new HashMap();

        foreach (filter_iterator($this, $predicate) as $key => $value) {
            $result->put($key, $value);
        }

        return $result;
    }

    public function map(callable $callback): HashMap
    {
        return map_by_reference($this->clone(), $callback);
    }

    /**
     * Iteratively reduces the map to a single value using a callback.
     *
     * @param callable $callback Accepts the carry and current value, and
     *                           returns an updated carry value.
     *
     * @param mixed $initial Optional initial carry value.
     *
     * @return mixed The carry value of the final iteration, or the initial
     *               value if the map was empty.
     */
    public function reduce(callback $callback, $initial = null)
    {
        return reduce($this, $callback, $initial);
    }


    /*** NOT SURE IF THESE TWO ARE NECESSARY. THEY DO NOTHING SMART. ***/
    public function putAll($collection)
    {
        guard_is_traversable($collection);

        foreach ($collection as $key => $value) {
            $this->put($key, $value);
        }
    }

    /**
     *
     */
    public function removeAll($keys)
    {
        guard_is_traversable($keys);

        foreach ($keys as $key) {
            $this->remove($key);
        }
    }

    /*** ***/

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return $this->table->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function sort(callable $comparator = null)
    {
        $this->table->sort($comparator);
    }

    /**
     * {@inheritDoc}
     */
    public function ksort(callable $comparator = null)
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
    public function get($key)
    {
        return $this->table->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function put($key, $value)
    {
        $this->table->put($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($key)
    {
        return $this->table->remove($key);
    }

    /**
     *
     */
    public function find($value)
    {
        return $this->table->find($value);
    }

    /**
     * {@inheritDoc}
     */
    public function keys(): Set
    {
        $keys = new HashSet();

        foreach ($this->table as $key => $value) {
            $keys->add($key);
        }

        return $keys;
    }

    /**
     * {@inheritDoc}
     */
    public function values(): Sequence
    {
        $values = new Vector();

        // @todo benchmark against pushAll
        foreach ($this->table as $key => $value) {
            $values->push($value);
        }

        return $values;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->put($offset, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }
}
