<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Strategy;

/**
 * - Count cars coming by for every traffic light
 * - Sort out traffic lights with no cars coming by
 * - Sort traffic lights per crossing by number of cars coming by
 * - Distribute time according to the share of cars coming by
 */
class AverageStrategy implements StrategyInterface
{

    const TIME_FACTOR = 10;

    /**
     * Base data (e.g. number of teams/libraries, scores for certain things, etc.)
     *
     * @var array
     */
    protected $baseData;

    /**
     * ExampleStrategy constructor.
     *
     * @param array $baseData
     */
    public function __construct(array $baseData)
    {
        $this->baseData = $baseData;
    }

    /**
     * @inheritDoc
     */
    public function solve(): array {
        // Prepare waitlist.
        $crossings = array_map(function ($crossing) {
            $crossing['waitlist'] = [];
            foreach ($crossing['incomingStreets'] as $streetName) {
                $crossing['waitlist'][$streetName] = 0;
            }

            return $crossing;
        }, $this->baseData['crossings']);

        // Add cars to waitlist.
        foreach ($this->baseData['cars'] as $car) {
            foreach ($car['streets'] as $streetName) {
                foreach ($crossings as &$crossing) {
                    if (array_key_exists($streetName, $crossing['waitlist'])) {
                        $crossing['waitlist'][$streetName]++;
                        continue 2;
                    }
                }
            }
        }

        // Filter out waitlist entries with 0.
        $crossings = array_map(function($crossing) {
            $crossing['waitlist'] = array_filter($crossing['waitlist'], function ($count) {
                return $count > 0;
            });

            $crossing['length'] = array_reduce($crossing['waitlist'], function ($sum, $count) {
                return $sum + $count;
            }, 0);

            return $crossing;
        }, $crossings);

        // Sort waitlist.
        $crossings = array_map(function ($crossing) {
            arsort($crossing['waitlist']);

            return $crossing;
        }, $crossings);

        // Build solution: switch traffic lights in order of waitlist.
        $solutions = [];
        foreach ($crossings as $index => $crossing) {
            // Get total time and adjust time factor.
            $timeTotal = intval($this->baseData['time']);
            $timeFactor = self::TIME_FACTOR > $timeTotal ? $timeTotal : self::TIME_FACTOR;

            // Create plans from the waitlist.
            $plan = [];
            foreach ($crossing['waitlist'] as $street => $count) {
                // Switch traffic light in relation to the number of cars coming by.
                $time = round(($count / $crossing['length']) * $timeFactor);
                if ($time === 0) {
                    $time = 1;
                }
                $plan[] = [$street, $time];
            }

            // Check if we have something to do at all.
            if (count($plan) === 0) {
                continue;
            }

            // Add solution.
            $solutions[] = [
                [$index],
                [count($plan)],
                ...$plan,
            ];
        }

        return $solutions;
    }

}
