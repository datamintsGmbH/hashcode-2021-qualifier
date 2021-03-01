<?php


namespace Datamints\HashCode\Qualifier2021\Strategy\Models;


class Car {
	public $streets = 0;
	public $path = [];

	/**
	 * @param array $streets
	 * @return array
	 */
	public function calculateRoute($time) {

		$route = \array_fill(0, $time, NULL);
		$path = 0;
		/** @var \Datamints\HashCode\Qualifier2021\Strategy\Models\Street $street */
		$street = $this->path[$path];
		$durationLeft = 0;
		for($i = 0; $i < $time; $i++) {
			if($durationLeft == 0) {
				$route[$i] = $street;
				$path++;
				if(!isset($this->path[$path])) {
					return $route;
				}
				$street = $this->path[$path];
				$durationLeft = $street->duration;
			} else {
				$durationLeft--;
			}
		}
		return $route;
	}
}