<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Strategy;

/**
 * Example strategy
 */
class Hash2021Strategy implements StrategyInterface
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
        $durchschnittStrassenlenge = 0;
        foreach ($this->baseData['cars'] as $car) {
            $lengeAnStrassen = count($car['streets']) - 1;
            $durchschnittStrassenlenge += $lengeAnStrassen;

            for ($i = 0; $i < $lengeAnStrassen; $i++) {
                $startStreet = $car['streets'][$i];

                foreach ($crossings as &$crossing) {
                    if (array_key_exists($startStreet, $crossing['waitlist'])) {
                        $crossing['waitlist'][$startStreet]++;
                        break;
                    }
                }
            }
        }
        $durchschnittStrassenlenge /= $this->baseData['numberStreets'];

        // Sort waitlist.
        $crossings = array_map(function ($crossing) {
            arsort($crossing['waitlist']);

            return $crossing;
        }, $crossings);

        // Collect traffic lights to switch.
        $crossingsToSwitch = [];
        foreach ($crossings as $index => $crossing) {
            $streetsTiming = [];
            foreach ($crossing['waitlist'] as $name => $anzahlAnDurchfahrten) {
                $streetsTiming[] = [
                    'street' => $name,
                    'delay' => ceil(($anzahlAnDurchfahrten + 1)*3 / $durchschnittStrassenlenge)
                ];
            }

            $crossingsToSwitch[] = [
                'index' => $index,
                'street' => $streetsTiming,
            ];
        }

        // Create output format.
        $solutions = array_map(function ($crossing) {
            $result = [
                [$crossing['index']],
                [count($crossing['street'])],
            ];
            foreach ($crossing['street'] as $streetsTiming) {
                $result[] = [$streetsTiming['street'], $streetsTiming['delay']];
            }
            return $result;
        }, $crossingsToSwitch);

        return $solutions;
    }

}
