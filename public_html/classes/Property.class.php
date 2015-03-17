<?php

class Property{

	var $id;
	var $arrPoints = array();

	var $arrVerticesX = array(); // An array of just the x values (Useful for basic sanity checking in collision detection)
	var $arrVerticesY = array(); // An array of just the y values


	function __construct( $id, $arrPoints ){

		$this->id 			= $id;
		$this->arrPoints 	= $arrPoints;

		foreach( $arrPoints as $thisPoint ){
			$this->arrVerticesX[] = $thisPoint['x'];
			$this->arrVerticesY[] = $thisPoint['y'];
		}

	}




	public function printMarkup(){
		$arrPath = $this->getPath();
		$html = '<path class="' . $arrPath['class'] . '" d="' . $arrPath['d'] . '" id="' . $arrPath['id'] . '" />';
		return $html;	
	}



	/**
	 Returns an associative array with 'class', 'id' and 'd' values
	*/
	public function getPath(){
		$arrPath = array();
		$arrPath['id'] = 'property' . $this->id;
		$arrPath['class'] = 'Property';
		$arrPath['d'] = 'M ';
		foreach( $this->arrPoints as $thisPoint ){
			$arrPath['d'] .= $thisPoint['x'] . ',' . $thisPoint['y'] . ' ';
		} 
		$arrPath['d'] .= 'z';
		return $arrPath;
	}




	/**
	 Takes an array of points, return the centre of them and the maximum radius
	*/
	function getCenterData(){

		$objMath = new Math();

		$arrFirstAveragePoint = $objMath->midPoint( $this->arrPoints[0], $this->arrPoints[2] );
		$arrSecondAveragePoint = $objMath->midPoint( $this->arrPoints[1], $this->arrPoints[3] );
		$arrCenterPoint = $objMath->midPoint( $arrFirstAveragePoint, $arrSecondAveragePoint );

		// Use the mid point to calculate which point is farthest away from center and define that as the radius 
		$farthestPointDistance = 0;
		$nearestPointDistance = INF;
		// Loop through all points finding the farthest
		foreach( $this->arrPoints as $thisPoint ){
			$thisDistance = $objMath->distanceBetween( $arrCenterPoint, $thisPoint );

			if( $nearestPointDistance > $thisDistance ){
				$nearestPointDistance = $thisDistance;
			}
			if( $farthestPointDistance < $thisDistance ){
				$farthestPointDistance = $thisDistance;
			}
		}

		$arrReturn = array();
		$arrReturn['arrCenterPoint'] = $arrCenterPoint;
		$arrReturn['nearestRadius'] = $nearestPointDistance;
		$arrReturn['farthestRadius'] = $farthestPointDistance;

		return $arrReturn;
	}




	/** 
	 Returns an associative array with 'class', 'd' values 
	 The front of a property is the 
	*/
	public function getFront(){
		$arrFront = array();
		$arrFront['class'] = 'Front';
		$arrFront['d'] = 'M ' . $this->arrPoints[0]['x'] . ',' . $this->arrPoints[0]['y'] . ' ' . $this->arrPoints[1]['x'] . ',' . $this->arrPoints[1]['y'];
		return $arrFront;
	}


}
