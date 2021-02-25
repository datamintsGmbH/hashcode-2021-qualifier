<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Strategy;

/**
 * Simple strategy: switch the traffic light with the highest number of waiting cars at start to green, then all others.
 */
class SimpleStrategyImproved implements StrategyInterface
{

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
            $startStreet = $car['streets'][0];
            foreach ($crossings as &$crossing) {
                if (array_key_exists($startStreet, $crossing['waitlist'])) {
                    $crossing['waitlist'][$startStreet]++;
                    break;
                }
            }
        }

        // Sort waitlist.
        $crossings = array_map(function ($crossing) {
            arsort($crossing['waitlist']);

            return $crossing;
        }, $crossings);

        // Build solution: switch traffic lights in order of waitlist.
        $solutions = [];
        foreach ($crossings as $index => $crossing) {
            // Get time from basedata to calculate with it.
            $time = $this->baseData['time'];

            // Create plans from the waitlist.
            $plan = [];
            foreach ($crossing['waitlist'] as $street => $numberCars) {
                // Switch traffic light until all cars are through OR time is up, but at least 1 second.
                $timeRequired = $numberCars == 0 ? 1 : $numberCars;
                $plan[] = [$street, $timeRequired > $time ? $time : $timeRequired];

                // Reduce remaining time.
                $time -= $timeRequired;

                // Break if time is up.
                if ($time <= 0) {
                    break;
                }
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
