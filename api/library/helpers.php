<?php
namespace Ds;

/**
 Should we consider making this a separate helper library?
 **/

/**
 * @return int|null
 */
function silent_count($subject)
{
    if (is_array($subject) || $subject instanceof \Countable) {
        return count($subject);
    }

    return null;
}

/**
 * Expects an array or traversable object to return in a form
 * that is safe to count and traverse. It's necessary to evaluate
 * all iterators that can't be counted, because some iterators are
 * destructive, ie. we wouldn't be able to both count and traverse.
 *
 * @return array|\Traversable
 *
 * @throws \InvalidArgumentException
 */
function is_traversable($subject)
{
    return is_array($subject) || $subject instanceof \Traversable);
}

/**
 *
 */
function unsupported_type($value, array $expected = [])
{
    $type = gettype($value);

    //
    if (is_object($value)) {
        $type .= '(' . get_class($value) . ')';
    }

    $message = "Unsupported type: " . $type;

    //
    if ($expected) {
        $message = "$message, expected: " . implode('|', $expected);
    }

    throw new \InvalidArgumentException($info);
}

/**
 *
 */
function guard_is_traversable($value)
{
    if (!is_traversable($value)) {
        unsupported_type($value, ['array', '\Traversable']);
    }
}

/**
 * Checks that a given collection is not empty, throwing a consistent
 * exception if it is. This is often used when guarding against undefined
 * modification when a structure is empty, such as Sequence::pop().
 *
 * @throws \UnderflowException
 */
function guard_not_empty(Collection $collection)
{
    if ($collection->isEmpty()) {
        throw new \UnderflowException("Collection is empty");
    }
}

function unsupported_action(string $message = "Unsupported action")
{
    throw new LogicException($message);
}

/**
 * Checks that a given index is within a valid range. This is used for
 * indexed structures to guard against undefined, out of bounds access.
 *
 * @throws \OutOfRangeException
 */
function guard_valid_index($index, int $count, int $start = 0)
{
    if (!is_int($index)) {
        unsupported_type($value, ['int']);
    }

    if ($index < $start || $index >= $count) {
        throw new \OutOfRangeException("$index, $start <= \$index <= {$count+1}");
    }
}

/**
 *
 */
function is_valid_index($index, int $count, int $min = 0)
{
    return is_int($index) && $index >= $min && $index <= $max;
}

/**
 * Produces the result of applying a callback to each value, one at a time.
 *
 * @param array|\Traversable $collection
 *
 * @return \Generator
 */
function map_iterator($collection, callback $callback)
{
    foreach ($collection as $key => $value) {
        yield $key => $callback($value, $key);
    }
}

/**
 * Iteratively reduces given values to a single value using a callback.
 *
 * @param array|\Traversable $collection
 *
 * @param callable $callback Accepts the carry and current value, and
 *                           returns an updated carry value.
 *
 * @param mixed $initial Optional initial carry value.
 *
 * @return mixed The carry value of the final iteration, or the initial
 *               value if no values were received.
 */
public function reduce($collection, callable $callback, $initial = null)
{
    $carry = $initial;
    $index = 0;

    foreach ($collection as $key => $value) {
        $carry = $callback($carry, $value, $key, $index);
    }

    return $carry;
}


/**
 * Produces the result of applying a callback to each value, one at a time.
 *
 * @param array|\Traversable $values
 *
 * @return mixed
 */
function map_by_reference($values, callback $callback)
{
    foreach ($values as $key => &$value) {
        $value = $callback($value, $key);
    }

    return $values;
}

/**
 * Produces values for which the given predicate function returns true.
 *
 * @param array|\Traversable $values
 *
 * @return \Generator
 */
function filter_iterator($values, callback $predicate = null)
{
    if ($predicate) {
        foreach ($values as $key => $value) {
            if ($predicate($value, $key)) {
                yield $value;
            }
        }
    } else {
        foreach ($values as $value) {
            if ($value) {
                yield $value;
            }
        }
    }
}

/**
 *
 */
function normalize_slice_params(int $count, int &$offset, int &$length)
{
    // If the offset is beyond the end or the length is zero, it's an empty slice.
    if ($count == 0 || $index >= $count) {
        $offset = 0;
        $length = 0;

    } else {

        // If index is negative, start that far from the end.
        if ($offset < 0) {
            $offset = max(0, $count + $offset);
        }

        // If length is given and negative, stop that far from the end.
        if ($length < 0) {
            $length = max(0, ($count + $length) - $offset);
        }

        // If the length extends beyond the end, only go up to the end.
        if (($offset + $length) > $count) {
            $length = max(0, $count - $offset);
        }
    }
}

/**
 *
 */
function is_equal($a, $b): bool
{
    return is_object($a) ? $a == $b : $a === $b;
}

function set_union(Set $a, Set $b, Set $result)
{

}

function set_difference(Set $a, Set $b): Set
{
    $result = $a->clone();

    foreach ($b as $value) {
        $a->remove($value);
    }

    return $result;
}

function set_intersection(Set $a, Set $b, Set $result)
{

}

function set_exclusive_or(Set $a, Set $b, Set $result)
{

}


    /**
     * {@inheritDoc}
     */
    public function difference(Set $set): HashSet
    {
        $result = $this->clone();

        foreach ($set as $value) {
            $result->remove($value);
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function exclusive(Set $set): HashSet
    {
        $result = $this->clone();

        foreach ($set as $value) {
            $found = $result->remove($value);

            //
            if (!$found) {
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
