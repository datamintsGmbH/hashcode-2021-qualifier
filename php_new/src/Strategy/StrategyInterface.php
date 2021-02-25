<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Strategy;

/**
 * Interface for solving strategies
 */
interface StrategyInterface
{

    /**
     * Generate solution.
     *
     * @return array
     */
    public function solve(): array;

}
