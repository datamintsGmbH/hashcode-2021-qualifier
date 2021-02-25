<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Strategy;

/**
 * XXX
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
            // Create plans from the waitlist.
            $plan = [];
            foreach ($crossing['waitlist'] as $street => $count) {
                // Switch traffic light until all cars are through OR time is up, but at least 1 second.
                $time = ceil(($count / $crossing['length']) * self::TIME_FACTOR);
                $plan[] = [$street, $time];
            }

            $solutions[] = [
                [$index],
                [count($plan)],
                ...$plan,
            ];
        }

        return $solutions;
    }

}
