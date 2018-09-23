<?php
namespace Ds\Structures;

use ArrayAccess;

use Ds\Interfaces\Collection;
use Ds\Interfaces\Map;
use Ds\Interfaces\Sortable;

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
