<?php

declare(strict_types=1);

namespace Datamints\HashCode\Qualifier2021\Strategy;

/**
 * Example strategy
 */
class ExampleStrategy implements StrategyInterface
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
    	$this->cars = $this->readCars($this->carCount, $this->streetCount + 1, $problems);
		//print_r($this->duration);

		$this->paths = [];
    	/** @var \Datamints\HashCode\Qualifier2021\Strategy\Models\Car $car */
		foreach ($this->cars as $key => $car) {
			$this->paths[] = $car->calculateRoute($this->duration);
		}

		$intersectionsDrivenOverCount = 0;
		$intersectionsDrivenOver = [];
		for($i = 0; $i < $this->intersectionCount; $i++) {
			$inter = \array_fill(0, $this->duration, NULL);

			$isUsed = false;
			foreach ($this->paths as $path) {
				/** @var \Datamints\HashCode\Qualifier2021\Strategy\Models\Street $street */
				foreach ($path as $key => $street) {
					if($street != NULL && $street->intersectionEnd === $i) {
						if($inter[$key] == NULL) {
							$inter[$key] = $street->name;
							$isUsed = true;
						}
					}
				}
			}

			if($isUsed) {
				$intersectionsDrivenOverCount++;
				$intersectionsDrivenOver[] = [$i, $inter];
			}
			echo $i;
			ob_flush();

		}

		$return = [];
		$return[] = [
			[$intersectionsDrivenOverCount]
		];

		foreach ($intersectionsDrivenOver as $inter) {
			$intersectionId = $inter[0];
			$streets = $inter[1];

			$return[] = [
				[$intersectionId]
			];

			$uniqueStreets = array_unique(array_filter($streets));
			$differentIncomingStreets = count($uniqueStreets);

			$return[] = [
				[$differentIncomingStreets]
			];

			$currentStreet = array_filter($streets)[array_key_first(array_filter($streets))];
			$streetCounts = [];
			foreach ($streets as $street) {
				if($street !== NULL) {
					$currentStreet = $street;
				}
				if(!isset($streetCounts[$currentStreet])) {
					$streetCounts[$currentStreet] = 0;
				}
				$streetCounts[$currentStreet] += 1;
			}

			foreach ($streetCounts as $streetName => $count) {

				$return[] = [
					[$streetName, $count]
				];
			}
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
