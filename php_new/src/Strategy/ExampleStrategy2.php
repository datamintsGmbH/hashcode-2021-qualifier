<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Strategy;

/**
 * Example strategy
 */
class ExampleStrategy2 implements StrategyInterface
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
    public function solve(array $problems): array {
    	ob_start();
    	#print_r($problems);

    	$this->duration = \intval($problems[0][0][0]);
    	$this->intersectionCount = \intval($problems[0][0][1]);
    	$this->streetCount = \intval($problems[0][0][2]);
    	$this->carCount = \intval($problems[0][0][3]);
    	$this->points = \intval($problems[0][0][4]);
    	$this->streets = $this->readStreets($this->streetCount, 1, $problems);
		//print_r($this->duration);
		$return[] = [
			[$this->intersectionCount],
		];

		for ($i = 0; $i < $this->intersectionCount; $i++) {
			$return[] = [
				[$i]
			];

			$streets = [];
			/** @var \Datamints\HashCode\Qualifier2021\Strategy\Models\Street $street */
			foreach ($this->streets as $street) {
				if($street->intersectionEnd === $i) {
					$cnt = rand(1,2);
					if($cnt > 0) {
						$streets[] = [$street->name, $cnt];
					}
				}
			}
			$return[] = [
				[count($streets)]
			];
			#shuffle($streets);
			$return[] = $streets;

			echo $i;
			ob_flush();

		}

        // @todo Implement real solution
        return $return;
    }

    public function readStreets($count, $start, $problems) {
    	$streets = [];
    	for($i = $start; $i < $start+$count; $i++) {
    		$currentStreet = $problems[$i][0];
    		$street = new \Datamints\HashCode\Qualifier2021\Strategy\Models\Street();
			$street->intersectionStart = \intval($currentStreet[0]);
			$street->intersectionEnd = \intval($currentStreet[1]);
			$street->name = $currentStreet[2];
			$street->duration = \intval($currentStreet[3]);
    		$streets[] = $street;
		}
    	return $streets;
	}

	public function readCars($count, $start, $problems) {
		$cars = [];
		for($i = $start; $i < $start+$count; $i++) {
			$currentCar = $problems[$i][0];
			$car = new \Datamints\HashCode\Qualifier2021\Strategy\Models\Car();
			$car->streets = \intval($currentCar[0]);

			for($j = 1; $j <= $car->streets;$j++) {
				$car->path[] = $this->getStreetByName($currentCar[$j]);
			}

			$cars[] = $car;
		}
		return $cars;
	}

	public function getStreetByName($streetName) {
    	/** @var \Datamints\HashCode\Qualifier2021\Strategy\Models\Street $street */
		foreach ($this->streets as $street) {
    		if($street->name === $streetName) {
    			return $street;
			}
		}
		return NULL;
	}

}
