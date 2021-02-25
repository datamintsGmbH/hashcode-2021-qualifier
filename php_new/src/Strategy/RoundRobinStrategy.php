<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Strategy;

/**
 * Round robin strategy: switch the traffic lights evenly distributed
 */
class RoundRobinStrategy implements StrategyInterface
{

    const TIME_PER_TRAFFIC_LIGHT = 10;

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
                // Switch traffic light evenly.
                //$plan[] = [$street, self::TIME_PER_TRAFFIC_LIGHT > $time ? $time : self::TIME_PER_TRAFFIC_LIGHT];
                $plan[] = [$street, self::TIME_PER_TRAFFIC_LIGHT];
                continue;

                // Reduce remaining time.
                $time -= self::TIME_PER_TRAFFIC_LIGHT;

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
