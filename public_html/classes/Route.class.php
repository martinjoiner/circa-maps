<?php

class Route{

	var $id = 0;
	var $width = 8;
	var $arrPoints = array();

	function __construct( $id, $arrPoints ){

		$this->id 			= intval($id);
		$this->arrPoints 	= $arrPoints;

	}




	/**
	 Returns the markup representation of this path for inclusion in an SVG file 
	*/
	public function printMarkup(){
		$arrPath = $this->getPath();
		$html = '<path class="' . $arrPath['class'] . '" stroke-width="' . $arrPath['stroke-width'] . '" d="' . $arrPath['d'] . '" id="' . $arrPath['id'] . '" />';
		return $html;	
	}




	/**
	 Returns an associative array with 'class', 'id' and 'd' values
	*/
	public function getPath(){
		$arrPath = array();
		$arrPath['id'] = 'route' . $this->id;
		$arrPath['class'] = 'Route';
		$arrPath['stroke-width'] = $this->width;
		$arrPath['d'] = 'M ';
		foreach( $this->arrPoints as $thisPoint ){
			$arrPath['d'] .= $thisPoint['x'] . ',' . $thisPoint['y'] . ' ';
		} 
		return $arrPath;
	}




	/**
	 Walks the route, totallying up the distance between each point to get a total length
	*/
	function calculateLength(){

	}




	/**
	 
	*/
	function gimme2NearestPoints( $x, $y ){

		$testPoint = array( 'x'=>$x, 'y'=>$y );

		$objMath = new Math();

		// Loop through all the points in arrPoints
		$arrScoreBoard = array();
		$arrScoreBoard[0] = array( 'distance'=>INF );
		$arrScoreBoard[1] = array( 'distance'=>INF );

		foreach( $this->arrPoints as $thisPoint ){
			$thisDistance = $objMath->distanceBetween( $testPoint, $thisPoint );
			if( $arrScoreBoard[1]['distance'] > $thisDistance ){
				$thisPoint['distance'] = $thisDistance;
				array_unshift( $arrScoreBoard, $thisPoint);
				$closestDistance = $thisDistance;
			} 
		}  

		$arrResult = array();
		$arrResult['top2NearestPoints'] = array_slice( $arrScoreBoard, 0, 2 );
		$arrResult['closestDistance'] = min( $arrScoreBoard[0]['distance'], $arrScoreBoard[1]['distance'] );
		$arrResult['routeID'] = $this->id;

		return $arrResult;
	}


}
