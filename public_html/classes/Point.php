<?php

class Point implements JsonSerializable{

	public $x;
	public $y;

	/** {integer} Used by Route when points are chained together */
	public $distance = INF;

	public function __construct( $x = 0, $y = 0 ){

		$this->x = intval($x);
		$this->y = intval($y);
		
	}




	/**
	 * Alters the location of a point by a random amount to fake organic positioning
	 *
	 * @param {integer} $maxVary number of units by which the point can vary on the x or y axis
	 */
	public function randomVary( $maxVary = 10 ){

		$this->x = $this->x + ( rand(0,$maxVary) - ($maxVary/2) );
		$this->y = $this->y + ( rand(0,$maxVary) - ($maxVary/2) );

		return $this;
		
	}

	public function jsonSerialize() {
        return [
            'x' => $this->x,
            'y' => $this->y
        ];
    }

}
