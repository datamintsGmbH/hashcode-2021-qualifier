<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Strategy;

/**
 * Simple strategy: switch the traffic light with the highest number of waiting cars at start to green forever.
 */
class SimpleStrategy implements StrategyInterface
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

        // Collect traffic lights to switch.
        $crossingsToSwitch = [];
        foreach ($crossings as $index => $crossing) {
            $streetToEnable = array_shift(array_keys($crossing['waitlist']));
            $crossingsToSwitch[] = [
                'index' => $index,
                'street' => $streetToEnable,
            ];
        }

        // Create output format.
        $solutions = array_map(function ($crossing) {
            return [
                [$crossing['index']],
                [1],
                [$crossing['street'], 1],
            ];
        }, $crossingsToSwitch);

        return $solutions;
    }

}
