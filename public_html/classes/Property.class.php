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

		foreach( $arrPoints as $thisPoint ){
			$this->arrVerticesX[] = $thisPoint['x'];
			$this->arrVerticesY[] = $thisPoint['y'];
		}

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
		if( $arrReturn['arrAreaData']['area'] < 100 || $arrReturn['arrAreaData']['area'] > 2400 ){
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
	 Returns an array of 8 points representing a fixed distance from the sides
	 $numDistance is how far away from the property the points are offset (breathing room basically)
	*/
	function getOffsetPoints( $numDistance = 5 ){

		// Negativise it
		$numDistance = 0 - $numDistance;

		$objMath = new Math();
		$arrReturn = array();
		$arrReturn[] = $objMath->pointDistanceBetweenPoints( $this->arrPoints[0], $this->arrPoints[1], $numDistance );
		$arrReturn[] = $objMath->pointDistanceBetweenPoints( $this->arrPoints[1], $this->arrPoints[2], $numDistance );
		$arrReturn[] = $objMath->pointDistanceBetweenPoints( $this->arrPoints[2], $this->arrPoints[3], $numDistance );
		$arrReturn[] = $objMath->pointDistanceBetweenPoints( $this->arrPoints[3], $this->arrPoints[0], $numDistance );
		$arrReturn[] = $objMath->pointDistanceBetweenPoints( $this->arrPoints[0], $this->arrPoints[3], $numDistance );
		$arrReturn[] = $objMath->pointDistanceBetweenPoints( $this->arrPoints[3], $this->arrPoints[2], $numDistance );
		$arrReturn[] = $objMath->pointDistanceBetweenPoints( $this->arrPoints[2], $this->arrPoints[1], $numDistance );
		$arrReturn[] = $objMath->pointDistanceBetweenPoints( $this->arrPoints[1], $this->arrPoints[0], $numDistance );

		return $arrReturn;
	}



	/** 
	 Replaces zero or more of the properties points with any inside arrPotentialPoints dependant on if improvement would result  
	 BUGGY! Needs to check collision with a route
	*/
	function improvePoints( $arrPotentialPoints ){
		
		$objMath = new Math();

		$cntPointsReplaced = 0;
		
		// Loop over all 4 points
		$iLimit = sizeof( $this->arrPoints );
		for( $i = 0; $i < $iLimit; $i++ ){

			// Get the nearestPoint from the array of potential points
			// TODO: An improvement would be to test the top 10 nearest points to see which ones avoid collision but allow improvement
			$nearestPointInArray = $objMath->nearestPointInArray( $this->arrPoints[$i], $arrPotentialPoints );

			// Record the current area
			$arrAreaDataPreChange = $this->getAreaData();


			if( $nearestPointInArray['distance'] < 100 ){
				
				// Replace the point with the nearest point from arrPotentialPoints
				$arrPointPreChange = $this->arrPoints[$i];
				$this->arrPoints[$i] = $nearestPointInArray['arrPointNearest'];

				// Test to see if your area has increased 
				$arrPostChangeInfo = $this->getInfo();

				// TODO: Need test for collision or too close to route

				if( $arrPostChangeInfo['arrAreaData']['area'] > $arrAreaDataPreChange['area'] && $arrPostChangeInfo['isStandard'] ){
					// If it has make the replacement a permenant change and update database
					$cntPointsReplaced++;
				} else {
					// If it has not increased, reverse the change
					$this->arrPoints[$i] = $arrPointPreChange;
				}

			}
			
		}

		if( $cntPointsReplaced ){
			// Save the new points in database
			$this->saveInDB();
		}

		$arrReturn = array();
		$arrReturn['cntPointsReplaced'] = $cntPointsReplaced;
		$arrReturn['path'] = $this->getPath();

		return $arrReturn;
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
