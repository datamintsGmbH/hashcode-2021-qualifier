<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Traits;

/**
 * Trait for extracting base data
 */
trait BaseDataTrait
{

    /**
     * Extract base data.
     *
     * @param array $inputLines Whole set of input lines
     * @return array
     */
    protected function extractBaseData(array $inputLines): array
    {
        // Read base data.
        list($time, $numberCrossings, $numberStreets, $numberCars, $bonus) = array_shift($inputLines);

        // Read streets.
        $streets = [];
        for ($i = 0; $i < $numberStreets; $i++) {
            list($start, $end, $name, $time) = array_shift($inputLines);
            $streets[] = compact('start', 'end', 'name', 'time');
        }

        // Read cars.
        $cars = [];
        for ($i = 0; $i < $numberCars; $i++) {
            $line = array_shift($inputLines);
            $cars[] = [
                'numberStreets' => array_shift($line),
                'streets' => $line,
            ];
        }

        // Build crossings.
        $crossings = [];
        for ($i = 0; $i < $numberCrossings; $i++) {
            $crossings[$i] = [
                'incomingStreets' => [],
                'outgoingStreets' => [],
            ];
        }
        foreach ($streets as $street) {
            $crossings[$street['start']]['outgoingStreets'][] = $street['name'];
            $crossings[$street['end']]['incomingStreets'][] = $street['name'];
        }

        // Combine everything.
        $baseData = compact('time', 'numberCrossings', 'numberStreets', 'numberCars', 'bonus', 'streets', 'cars', 'crossings');

        return $baseData;
    }

}
