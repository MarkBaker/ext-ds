<?php
namespace Ds\Interfaces;

/**
 * Indicates that a structure can be represented as an array.
 */
interface Arrayable
{
    /**
     * @return array The values of this structure as an array.
     */
    function toArray(): array;
}
