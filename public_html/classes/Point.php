<?php

class Point {

	public $x;
	public $y;

	public function __construct( $x, $y ){

		$this->x = intval($x);
		$this->y = intval($y);
		
	}

}
