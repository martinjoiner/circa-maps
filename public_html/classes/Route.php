<?php

class Route{

	/** {integer} Database ID of route */
	private $id = 0;

	/** {integer} Stroke width (when rendered on SVG) */
	private $width = 8;

	/** {array}  */
	private $arrPoints = [];




	/**
	 * @constructor 
	 *
	 * @param {integer} $id
	 * @param {array} $arrPoints Array of instances of Point class
	 */
	public function __construct( $id, $arrPoints ){

		$this->id 			= intval($id);
		$this->arrPoints 	= $arrPoints;

	}




	/**
	 * Makes the markup representation of this path for inclusion in an SVG file 
	 *
	 * @return {string} HTML markup
	 */
	public function printMarkup(){
		$arrPath = $this->getPath();
		$html = '<path class="' . $arrPath['class'] . '" stroke-width="' . $arrPath['stroke-width'] . '" d="' . $arrPath['d'] . '" id="' . $arrPath['id'] . '" />';
		return $html;	
	}




	/**
	 * Returns all the information for rendering the item on an SVG 
	 *
	 * @return {array} Containing 'class', 'id' and 'd' values
	 */
	public function getPath(){
		$arrPath = array();
		$arrPath['id'] = 'route' . $this->id;
		$arrPath['class'] = 'Route';
		$arrPath['stroke-width'] = $this->width;
		$arrPath['d'] = 'M ';
		foreach( $this->arrPoints as $thisPoint ){
			$arrPath['d'] .= $thisPoint->x . ',' . $thisPoint->y . ' ';
		} 
		return $arrPath;
	}




	/**
	 * Walks the route, totallying up the distance between each point to get a total length
	 *
	 * @return {integer} The total length of the whole route
	 */
	function calculateLength(){

	}




	/**
	 * Returns the 2 nearest points on a route
	 *
	 * @param {Point} The origin position from which to search
	 *
	 * @return {array} Contains 'top2NearestPoints', 'closestDistance' and 'routeID'
	 */
	public function gimme2NearestPoints( Point $point ){

		$objMath = new Math();

		// Loop through all the points in arrPoints
		$arrScoreBoard = [];
		$arrScoreBoard[0] = new Point();
		$arrScoreBoard[1] = new Point();

		// Iterate over all points on a route
		foreach( $this->arrPoints as $thisPoint ){
			$thisDistance = $objMath->distanceBetween( $point, $thisPoint );
			if( $arrScoreBoard[1]->distance > $thisDistance ){
				$thisPoint->distance = $thisDistance;
				array_unshift( $arrScoreBoard, $thisPoint);
				$closestDistance = $thisDistance;
			} 
		}  

		$arrResult = [];
		$arrResult['top2NearestPoints'] = array_slice( $arrScoreBoard, 0, 2 );
		$arrResult['closestDistance'] = min( $arrScoreBoard[0]->distance, $arrScoreBoard[1]->distance );
		$arrResult['routeID'] = $this->id;

		return $arrResult;
	}




	/** 
	 * Returns any segments of the route that contain a point within distance limit
	 * (Used by Map to only load data inside a limited area when checking for collisions)
	 *
	 * @param {array} $arrPointOrigin
	 * @param {integer} $distanceLimit
	 *
	 * @return {array} The segments of this route within radius
	 */
	public function getSegmentsWithinRange( $arrPointOrigin, $distanceLimit = 200 ){
		
		$objMath = new Math();

		$arrSegments = [];

		// Because the loop takes the point at i *and* the next one we only need to go to 1 before end of array
		$iLimit = sizeof($this->arrPoints) - 1;
		for( $i = 0; $i < $iLimit; $i++ ){
			$arrPointA = $this->arrPoints[$i];
			$arrPointB = $this->arrPoints[$i+1];

			$distanceA = $objMath->distanceBetween( $arrPointOrigin, $arrPointA );
			$distanceB = $objMath->distanceBetween( $arrPointOrigin, $arrPointB );

			// If either point is within distance, add the segment to return
			if( $distanceA < $distanceLimit || $distanceB < $distanceLimit ){
				$arrSegments[] = array( $arrPointA, $arrPointB );
			} 
		}

		return $arrSegments;
	}


}
