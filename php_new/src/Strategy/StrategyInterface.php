<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Strategy;

/**
 * Interface for solving strategies
 */
interface StrategyInterface
{

    /**
     * Generate solution for an inout line.
     *
     * @param array $inputData Input data
     * @return array
     */
    public function solve(array $inputData): array;

}
