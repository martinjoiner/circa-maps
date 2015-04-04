<?php

class Property{

	var $arrPoints = array();
	var $id;
	var $mapID; 

	var $arrVerticesX = array(); // An array of just the x values (Useful for basic sanity checking in collision detection)
	var $arrVerticesY = array(); // An array of just the y values




	/**
	 A property can be constructed with just an array of points and a mapID
	 passing and an id is optional
	*/
	function __construct( $arrPoints, $mapID = NULL, $id = NULL ){

		$this->arrPoints = $arrPoints;

		if( $mapID ){
			$this->mapID = intval($mapID);
		}
		if( $id ){
			$this->id = intval($id);
		}

		$this->calcVertices();
	}




	/**
	 Populates the arrVerticesX and arrVerticesY variables
	 (these are used in collision detection)
	*/
	private function calcVertices(){

		$this->arrVerticesX = array();
		$this->arrVerticesY = array();

		foreach( $this->arrPoints as $thisPoint ){
			$this->arrVerticesX[] = $thisPoint['x'];
			$this->arrVerticesY[] = $thisPoint['y'];
		}

	}



	
	public function replacePoint( $arrayPointer, $arrPointReplacement ){
		$this->arrPoints[$arrayPointer] = $arrPointReplacement;
		$this->calcVertices();
	}




	/**
	 Returns the XML markup of the path
	*/
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
		$arrReturn['arrAreaData'] = $this->getAreaData();
		$arrReturn['id'] = $this->id;
		$arrReturn['isStandard'] = true;

		// Area should be between 100 and 1600
		if( $arrReturn['arrAreaData']['area'] < 100 || $arrReturn['arrAreaData']['area'] > 3200 ){
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

		$arrReturn['doSidesIntersect'] = $this->doSidesIntersect();
		if( $arrReturn['doSidesIntersect'] ){
			$arrReturn['isStandard'] = false;
		}

		return $arrReturn;
	}




	/**
	 Returns an associative array containing area of the property and for debugging purpsoses the right-angled triangles that were used to calculate it
	*/
	public function getAreaData(){

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

		if( $disection1Length == 0 || $disection2Length == 0 ){
			return false;
		}

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

	*/
	function doSidesIntersect(){
		$objCoordinateGeometry = new CoordinateGeometry();
		$arrSide[] = array( $this->arrPoints[0], $this->arrPoints[1] );
		$arrSide[] = array( $this->arrPoints[1], $this->arrPoints[2] );
		$arrSide[] = array( $this->arrPoints[2], $this->arrPoints[3] );
		$arrSide[] = array( $this->arrPoints[3], $this->arrPoints[0] );
		if( $objCoordinateGeometry->doSegmentsIntersect( $arrSide[0], $arrSide[2] ) ){
			return true;
		}
		if( $objCoordinateGeometry->doSegmentsIntersect( $arrSide[1], $arrSide[3] ) ){
			return true;
		}
		return false;
	}




	/**
	 Compares the length of all sides, finds shortest and longest
	 Returns true or false to indicate if difference between shortest and longest is within limit.
	 $minShortPercentageOfLong The minimum percentage that shortest must be of longest 
	*/
	function areSideLengthsWithinLimits( $minShortPercentageOfLong = 20 ){

		$objMath = new Math();

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




	/**
	 Returns an array of 4 sides, each side contains 2 points. A side represents a paralel line offset from the side of the building
	 $numDistance is how far away from the property the sides are offset (breathing room basically)
	*/
	function getOffsetSides( $numDistance = 3 ){

		$arrReturn = array();

		$arrReturn[] = $this->calculateOffsetSide( 0, 1, $numDistance );
		$arrReturn[] = $this->calculateOffsetSide( 1, 2, $numDistance );
		$arrReturn[] = $this->calculateOffsetSide( 2, 3, $numDistance );
		$arrReturn[] = $this->calculateOffsetSide( 3, 0, $numDistance );

		return $arrReturn;
	}




	private function calculateOffsetSide( $numPoint1, $numPoint2, $numDistance ){

		$objMath = new Math();
		$arrSide = array();

		$arrPointProjected = $objMath->ninetyDeg( $this->arrPoints[$numPoint1], $this->arrPoints[$numPoint2], false );
		$arrSide[0] = $objMath->pointDistanceBetweenPoints( $this->arrPoints[$numPoint2], $arrPointProjected, $numDistance );

		$arrPointProjected = $objMath->ninetyDeg( $this->arrPoints[$numPoint2], $this->arrPoints[$numPoint1], true );
		$arrSide[1] = $objMath->pointDistanceBetweenPoints( $this->arrPoints[$numPoint1], $arrPointProjected, $numDistance );

		return $arrSide;
	}




	public function getSide( $numSide ){
		switch( $numSide ){
			case 0: return array( $this->arrPoints[0], $this->arrPoints[1] );
			case 1: return array( $this->arrPoints[1], $this->arrPoints[2] );
			case 2: return array( $this->arrPoints[2], $this->arrPoints[3] );
			case 3: return array( $this->arrPoints[3], $this->arrPoints[0] );
		}
	}




	public function replaceSide( $numSide, $arrSide ){

		switch( $numSide ){
			case 0: $this->arrPoints[0] = $arrSide[0]; $this->arrPoints[1] = $arrSide[1]; break;
			case 1: $this->arrPoints[1] = $arrSide[0]; $this->arrPoints[2] = $arrSide[1]; break;
			case 2: $this->arrPoints[2] = $arrSide[0]; $this->arrPoints[3] = $arrSide[1]; break;
			case 3: $this->arrPoints[3] = $arrSide[0]; $this->arrPoints[0] = $arrSide[1]; break;
		}
		$this->calcVertices();
	}




	// Checks if one of the points matches the provided point
	function hasPointWithSameCoords( $arrPointCheck ){
		// Loop through all points finding the farthest
		foreach( $this->arrPoints as $thisPoint ){
			if( $arrPointCheck['x'] === $thisPoint['x'] && $arrPointCheck['y'] === $thisPoint['y'] ){
				return true;
			}
		}
		return false;
	}




	/** 
	 Creates or updates the database
	*/
	public function saveInDB(){

		include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

		if( is_null($this->id) ){
			// If id is null we need to create it in the database
			$qry = $db->prepare("	INSERT INTO `property` ( `map_id`, `name` )
									VALUES ( :mapID, 'new Property' );
								");
			$qry->bindValue('mapID', $this->mapID, PDO::PARAM_INT);
			$qry->execute();
			$this->id = $db->lastInsertId();
		} else {
			// If id is not null we should clear any poitns in the database attached to this property
			$qry = $db->prepare("	DELETE FROM `point` 
									WHERE `property_id`= :propertyID
								");
			$qry->bindValue('propertyID', $this->id, PDO::PARAM_INT);
			$qry->execute();
		}

		// Insert rows for the points
		$qry = $db->prepare("	INSERT INTO `point` ( `property_id`, `order`, `x`, `y` )
								VALUES 	( :propertyID, 1, :x1, :y1 ),
										( :propertyID, 2, :x2, :y2 ),
										( :propertyID, 3, :x3, :y3 ),
										( :propertyID, 4, :x4, :y4 );
							");
		$qry->bindValue('propertyID', $this->id, PDO::PARAM_INT);
		$qry->bindValue('x1', $this->arrPoints[0]['x'], PDO::PARAM_INT);
		$qry->bindValue('y1', $this->arrPoints[0]['y'], PDO::PARAM_INT);
		$qry->bindValue('x2', $this->arrPoints[1]['x'], PDO::PARAM_INT);
		$qry->bindValue('y2', $this->arrPoints[1]['y'], PDO::PARAM_INT);
		$qry->bindValue('x3', $this->arrPoints[2]['x'], PDO::PARAM_INT);
		$qry->bindValue('y3', $this->arrPoints[2]['y'], PDO::PARAM_INT);
		$qry->bindValue('x4', $this->arrPoints[3]['x'], PDO::PARAM_INT);
		$qry->bindValue('y4', $this->arrPoints[3]['y'], PDO::PARAM_INT);
		$qry->execute();
		$qry->closeCursor();

	}



}
