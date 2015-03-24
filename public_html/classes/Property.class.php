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




	/**
	 Returns an associative array of information about the property
	 Runs a series of checks to determine if the property is a standard (sensible) shape
	*/
	public function getInfo(){
		$arrReturn = array();
		$arrReturn['area'] = $this->getArea();
		$arrReturn['isStandard'] = true;

		// Area should be between 10 and 1600
		if( $arrReturn['area'] < 100 || $arrReturn['area'] > 1600 ){
			$arrReturn['isStandard'] = false;
		}

		$arrReturn['areDisectionsWithinLimits'] = $this->areDisectionsWithinLimits();
		if( !$arrReturn['areDisectionsWithinLimits'] ){
			$arrReturn['isStandard'] = false;
		}

		$arrReturn['areSideLengthsWithinLimits'] = $this->areSideLengthsWithinLimits();
		if( !$arrReturn['areSideLengthsWithinLimits'] ){
			$arrReturn['isStandard'] = false;
		}

		return $arrReturn;
	}




	/**
	 Returns an associative array containing area of the property and for debugging purpsoses the right-angled triangles that were used to calculate it
	*/
	public function getArea(){

		$objMath = new Math();

		$arrReturn['area'] = 0;
		$arrReturn['rightAngledTriangles'] = array();

		$disection1Length = $objMath->distanceBetween( $this->arrPoints[0], $this->arrPoints[2] );
		$disection2Length = $objMath->distanceBetween( $this->arrPoints[1], $this->arrPoints[3] );

		if( $disection1Length > $disection2Length ){

			$result = $objMath->areaOfTriangle( $this->arrPoints[1], $this->arrPoints[0], $this->arrPoints[2] );
			$arrReturn['area'] += $result['area'];
			$arrReturn['rightAngledTriangles'] = array_merge( $arrReturn['rightAngledTriangles'], $result['rightAngledTriangles'] );

			$result = $objMath->areaOfTriangle( $this->arrPoints[3], $this->arrPoints[0], $this->arrPoints[2] );
			$arrReturn['area'] += $result['area'];
			$arrReturn['rightAngledTriangles'] = array_merge( $arrReturn['rightAngledTriangles'], $result['rightAngledTriangles'] );
			
		} else {

			$result = $objMath->areaOfTriangle( $this->arrPoints[0], $this->arrPoints[1], $this->arrPoints[3] );
			$arrReturn['area'] += $result['area'];
			$arrReturn['rightAngledTriangles'] = array_merge( $arrReturn['rightAngledTriangles'], $result['rightAngledTriangles'] );

			$result = $objMath->areaOfTriangle( $this->arrPoints[2], $this->arrPoints[1], $this->arrPoints[3] );
			$arrReturn['area'] += $result['area'];
			$arrReturn['rightAngledTriangles'] = array_merge( $arrReturn['rightAngledTriangles'], $result['rightAngledTriangles'] );

		}

		return $arrReturn;

	}




	/**
	 Compares the distance between points 0 and 2 to the distance between points 1 and 3. 
	 Returns true or false to indicate if difference in size is within limit. 
	 $minShortPercentageOfLong The minimum percentage that shortest must be of longest
	*/
	function areDisectionsWithinLimits( $minShortPercentageOfLong = 80 ){

		$objMath = new Math();

		$disection1Length = $objMath->distanceBetween( $this->arrPoints[0], $this->arrPoints[2] );
		$disection2Length = $objMath->distanceBetween( $this->arrPoints[1], $this->arrPoints[3] );

		$shortestDisectionLength = min( $disection1Length, $disection2Length );
		$longestDisectionLength = max( $disection1Length, $disection2Length );

		// What is the shortest disection expressed as a percentage of the longest disection
		$shortPercentageOfLong = $shortestDisectionLength / $longestDisectionLength * 100;

		if( $shortPercentageOfLong < $minShortPercentageOfLong ){
			return false;
		} else {
			return true;
		}

	}




	/**
	 Compares the length of all sides, finds shortest and longest
	 Returns true or false to indicate if difference between shortest and longest is within limit.
	 $minShortPercentageOfLong The minimum percentage that shortest must be of longest 
	*/
	function areSideLengthsWithinLimits( $minShortPercentageOfLong = 20 ){

		$objMath = new Math();

		// Area should be between 10 and 1600
		$arrSideLengths[] = $objMath->distanceBetween( $this->arrPoints[0], $this->arrPoints[1] );
		$arrSideLengths[] = $objMath->distanceBetween( $this->arrPoints[1], $this->arrPoints[2] );
		$arrSideLengths[] = $objMath->distanceBetween( $this->arrPoints[2], $this->arrPoints[3] );
		$arrSideLengths[] = $objMath->distanceBetween( $this->arrPoints[3], $this->arrPoints[0] );

		$shortestSideLength = min( $arrSideLengths );
		$longestSideLength = max( $arrSideLengths );

		$shortPercentageOfLong = $shortestSideLength / $longestSideLength * 100;

		if( $shortPercentageOfLong < $minShortPercentageOfLong ){
			return false;
		} else {
			return true;
		}

	}



}
