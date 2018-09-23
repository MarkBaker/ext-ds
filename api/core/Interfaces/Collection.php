<?php
namespace Ds\Interfaces;

use Countable;
use IteratorAggregate;

/**
 * Generic collection with basic functionality shared by most structures.
 */
interface Collection extends Countable, IteratorAggregate, Arrayable
{
    /**
     * Clears or resets the collection to an initial state.
     */
    function clear();

    /**
     * @return bool TRUE if this collection is empty, FALSE otherwise.
     */
    function isEmpty(): bool;
}
