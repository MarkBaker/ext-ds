<?php

interface Graph
{
    function add($vertex);

    function remove($vertex): bool;

    function has($vertex): bool;

    function link($vertex, $target, $weight = 1);

    function isCyclic(): bool;

    function isLinked($vertex, $target): bool;

    function isReachable($vertex, $target);

    function getShortestPath($vertex, $target): Sequence;

    function getLongestPath($vertex, $target): Sequence;

    function getWeight($vertex, $target);

    function getMinimumWeight($vertex, $target);

    function getMaximumWeight($vertex, $target);

    function depthFirst(): Iterator;

    function breadthFirst(): Iterator;
}
