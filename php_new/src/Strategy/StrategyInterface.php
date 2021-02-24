<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Strategy;

/**
 * Interface for solving strategies
 */
interface StrategyInterface
{

    /**
     * Generate solution for a set of problems;
     * this should return an array of arrays:
     * [
     *   Solution: [
     *     Lines of the solution: [], []
     *   ]
     * ]
     *
     * @param array $problems List of problems
     * @return array
     */
    public function solve(array $problems): array;

}
