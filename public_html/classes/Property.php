<?php

/**
 * A property is a 4-sided shape (self-closing polygon)
 */
class Property{

	/** {array} of Points that define this property boundary */
	public $arrPoints = [];

	/** Database ID of the property (public so CoordinateGeometry can access it) */
	public $id;

	/** {integer} Database ID of the map that this Property belongs to */
	private $mapID; 

	/** An array of just the x values (Useful for fast sanity checking in collision detection) */
	public $arrVerticesX = []; 

	/** An array of just the y values */
	public $arrVerticesY = []; 




	/**
	 * A property can be constructed with just an array of points and a mapID
	 * passing and an id is optional
	 *
	 * @param {array} $arrPoints Array of instances of Point class
	 * @param {integer|string} $mapID
	 * @param {integer|string} $id
	 */
	public function __construct( $arrPoints, $mapID = NULL, $id = NULL ){

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
	 * Populates the arrVerticesX and arrVerticesY variables
	 * (these are used in collision detection)
	 */
	private function calcVertices(){

		// Empty any previous values
		$this->arrVerticesX = [];
		$this->arrVerticesY = [];

		foreach( $this->arrPoints as $thisPoint ){
			$this->arrVerticesX[] = $thisPoint->x;
			$this->arrVerticesY[] = $thisPoint->y;
		}

	}



	
	/**
	 * Setter method for a particular Point
	 *
	 * @param {integer} $arrayPointer Which side to replace
	 * @param {array} $replacementPoint
	 */
	public function replacePoint( $arrayPointer, Point $replacementPoint ){
		$this->arrPoints[$arrayPointer] = $replacementPoint;
		$this->calcVertices();
	}




	/**
	 * Returns the XML markup of the path
	 */
	public function printMarkup(){
		$arrPath = $this->getPath();
		$html = '<path class="' . $arrPath['class'] . '" d="' . $arrPath['d'] . '" id="' . $arrPath['id'] . '" />';
		return $html;	
	}




	/**
	 * Returns an associative array with 'class', 'id' and 'd' values
	 *
	 * @return {array}
	 */
	public function getPath(){
		$arrPath = array();
		$arrPath['id'] = 'property' . $this->id;
		$arrPath['class'] = 'Property';
		$arrPath['d'] = 'M ';
		foreach( $this->arrPoints as $thisPoint ){
			$arrPath['d'] .= $thisPoint->x . ',' . $thisPoint->y . ' ';
		} 
		$arrPath['d'] .= 'z';
		return $arrPath;
	}




	/**
	 * Takes an array of points, return the centre of them and the maximum radius
	 *
	 * @return {array} Contains: 'centerPoint', 'nearestRadius' and 'farthestRadius'
	 */
	public function getCenterData(){

		$centerPoint = $this->centerPoint();

		// Use the mid point to calculate which point is farthest away from center and define that as the radius 
		$farthestPointDistance = 0;
		$nearestPointDistance = INF;

		// Loop through all points finding the farthest
		foreach( $this->arrPoints as $thisPoint ){
			$thisDistance = Math::distanceBetween( $centerPoint, $thisPoint );

			if( $nearestPointDistance > $thisDistance ){
				$nearestPointDistance = $thisDistance;
			}
			if( $farthestPointDistance < $thisDistance ){
				$farthestPointDistance = $thisDistance;
			}
		}

		return [
			'centerPoint' => $centerPoint,
			'nearestRadius' => $nearestPointDistance,
			'farthestRadius' => $farthestPointDistance
		];

	}




	/**
	 * Gets the center of the property
	 *
	 * @return {Point}
	 */
	public function centerPoint(){
		$firstAveragePoint = Math::midPoint( $this->arrPoints[0], $this->arrPoints[2] );
		$secondAveragePoint = Math::midPoint( $this->arrPoints[1], $this->arrPoints[3] );
		return Math::midPoint( $firstAveragePoint, $secondAveragePoint );
	}




	/**
	 * Tests if a Point is inside the property
	 *
	 * @param {Point} $point
	 *
	 * @return {boolean}
	 */
	public function coversPoint( Point $point ){

		// Number of vertices
		$points_polygon = count($this->arrPoints); 

		if( Math::isInPolygon($points_polygon, $this->arrVerticesX, $this->arrVerticesY, $point ) ){
			return true;
		}

		return false;
	}




	/** 
	 * Returns data needed to render fronts on SVG canvas
	 * The front of a property is the 1st and 2nd points in arrPoints
	 *
	 * @return {array} Contains: 'class' and 'd'
	 */
	public function getFront(){
		$arrFront = [ 'class' => 'Front', 'd'=>'' ];
		$arrFront['d'] = 'M ' . $this->arrPoints[0]->x . ',' . $this->arrPoints[0]->y . ' ' . $this->arrPoints[1]->x . ',' . $this->arrPoints[1]->y;
		return $arrFront;
	}




	/**
	 * Returns an associative array of information about the property
	 * Runs a series of checks to determine if the property is a standard (sensible) shape
	 *
	 * @return {array} Contains: 'arrAreaData', 'id', 'isStandard'
	 */
	public function getInfo(){

		$arrReturn = [];
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
	 * Returns an associative array containing area of the property and for debugging purpsoses the right-angled triangles that were used to calculate it
	 *
	 * @return {array} Contains: {float} 'area', {array} 'rightAngledTriangles'
	 */
	public function getAreaData(){

		$arrReturn['area'] = 0;
		$arrReturn['rightAngledTriangles'] = [];

		$disection1Length = Math::distanceBetween( $this->arrPoints[0], $this->arrPoints[2] );

		$disection2Length = Math::distanceBetween( $this->arrPoints[1], $this->arrPoints[3] );

		if( $disection1Length > $disection2Length ){

			$result = Math::areaOfTriangle( $this->arrPoints[1], $this->arrPoints[0], $this->arrPoints[2] );
			$arrReturn['area'] += $result['area'];
			$arrReturn['rightAngledTriangles'] = array_merge( $arrReturn['rightAngledTriangles'], $result['rightAngledTriangles'] );

			$result = Math::areaOfTriangle( $this->arrPoints[3], $this->arrPoints[0], $this->arrPoints[2] );
			$arrReturn['area'] += $result['area'];
			$arrReturn['rightAngledTriangles'] = array_merge( $arrReturn['rightAngledTriangles'], $result['rightAngledTriangles'] );

		} else {

			$result = Math::areaOfTriangle( $this->arrPoints[0], $this->arrPoints[1], $this->arrPoints[3] );
			$arrReturn['area'] += $result['area'];
			$arrReturn['rightAngledTriangles'] = array_merge( $arrReturn['rightAngledTriangles'], $result['rightAngledTriangles'] );

			$result = Math::areaOfTriangle( $this->arrPoints[2], $this->arrPoints[1], $this->arrPoints[3] );
			$arrReturn['area'] += $result['area'];
			$arrReturn['rightAngledTriangles'] = array_merge( $arrReturn['rightAngledTriangles'], $result['rightAngledTriangles'] );

		}

		return $arrReturn;

	}




	/**
	 * Compares the distance between points 0 and 2 to the distance between points 1 and 3. 
	 * Returns true or false to indicate if difference in size is within limit. 
	 * $minShortPercentageOfLong The minimum percentage that shortest must be of longest
	 *
	 * @param {integer} $minShortPercentageOfLong
	 */
	public function areDisectionsWithinLimits( $minShortPercentageOfLong = 80 ){

		$disection1Length = Math::distanceBetween( $this->arrPoints[0], $this->arrPoints[2] );
		$disection2Length = Math::distanceBetween( $this->arrPoints[1], $this->arrPoints[3] );

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
	 * Is the property a funny 'hour glass' / 'figure 8' shape
	 *
	 * @return {boolean}
	 */
	public function doSidesIntersect(){
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
	 * Compares the length of all sides, finds shortest and longest
	 * Returns true or false to indicate if difference between shortest and longest is within limit.
	 * $minShortPercentageOfLong The minimum percentage that shortest must be of longest 
	 *
	 * @param {integer} $minShortPercentageOfLong Minimum different between the lonest side and the shortest side
	 *
	 * @return {boolean}
	 */
	public function areSideLengthsWithinLimits( $minShortPercentageOfLong = 20 ){

		$arrSideLengths[] = Math::distanceBetween( $this->arrPoints[0], $this->arrPoints[1] );
		$arrSideLengths[] = Math::distanceBetween( $this->arrPoints[1], $this->arrPoints[2] );
		$arrSideLengths[] = Math::distanceBetween( $this->arrPoints[2], $this->arrPoints[3] );
		$arrSideLengths[] = Math::distanceBetween( $this->arrPoints[3], $this->arrPoints[0] );

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
	 * Returns an array of 4 sides, each side contains 2 points. A side represents a paralel line offset from the side of the building
	 *
	 * @param {integer} $numDistance How far away from the property the sides are offset (This amounts to setting the breathing room between properties basically)
	 *
	 * @return {array}
	 */
	public function getOffsetSides( $numDistance = 3 ){

		$arrResult = [];

		$arrResult[] = $this->calculateOffsetSide( 0, 1, $numDistance );
		$arrResult[] = $this->calculateOffsetSide( 1, 2, $numDistance );
		$arrResult[] = $this->calculateOffsetSide( 2, 3, $numDistance );
		$arrResult[] = $this->calculateOffsetSide( 3, 0, $numDistance );

		return $arrResult;
	}




	/**
	 * Produces a new side that is offset by a given distance from one of the existing sides
	 * 
	 * @param {integer} $numPoint1
	 * @param {integer} $numPoint2
	 * @param {integer} $numDistance
	 *
	 * @return {array}
	 */
	private function calculateOffsetSide( $numPoint1, $numPoint2, $numDistance ){

		$arrSide = [];

		$arrPointProjected = Math::ninetyDeg( $this->arrPoints[$numPoint1], $this->arrPoints[$numPoint2], false );
		$arrSide[0] = Math::pointDistanceBetweenPoints( $this->arrPoints[$numPoint2], $arrPointProjected, $numDistance );

		$arrPointProjected = Math::ninetyDeg( $this->arrPoints[$numPoint2], $this->arrPoints[$numPoint1], true );
		$arrSide[1] = Math::pointDistanceBetweenPoints( $this->arrPoints[$numPoint1], $arrPointProjected, $numDistance );

		return $arrSide;
	}




	/**
	 *
	 * @param {integer} 
	 *
	 * @return {array}
	 */
	public function getSide( $numSide ){
		switch( $numSide ){
			case 0: return array( $this->arrPoints[0], $this->arrPoints[1] );
			case 1: return array( $this->arrPoints[1], $this->arrPoints[2] );
			case 2: return array( $this->arrPoints[2], $this->arrPoints[3] );
			case 3: return array( $this->arrPoints[3], $this->arrPoints[0] );
		}
	}




	/**
	 * Replaces 2 points (eg. A 'side' of the Property)
	 *
	 * @param {integer} $numSide
	 * @param {array} $arrSide
	 */
	public function replaceSide( $numSide, $arrSide ){

		switch( $numSide ){
			case 0: $this->arrPoints[0] = $arrSide[0]; $this->arrPoints[1] = $arrSide[1]; break;
			case 1: $this->arrPoints[1] = $arrSide[0]; $this->arrPoints[2] = $arrSide[1]; break;
			case 2: $this->arrPoints[2] = $arrSide[0]; $this->arrPoints[3] = $arrSide[1]; break;
			case 3: $this->arrPoints[3] = $arrSide[0]; $this->arrPoints[0] = $arrSide[1]; break;
		}
		$this->calcVertices();
	}




	/**
	 * Checks if one of the points matches the provided point
	 *
	 * @param {array} 
	 *
	 * @return {boolean} 
	 */
	public function hasMatchingPoint( Point $pointToCheck ){
		// Iterate over all points 
		foreach( $this->arrPoints as $thisPoint ){
			if( $pointToCheck->x === $thisPoint->x && $pointToCheck->y === $thisPoint->y ){
				return true;
			}
		}
		return false;
	}




	/** 
	 * Creates or updates the database
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
		$qry->bindValue('x1', $this->arrPoints[0]->x, PDO::PARAM_INT);
		$qry->bindValue('y1', $this->arrPoints[0]->y, PDO::PARAM_INT);
		$qry->bindValue('x2', $this->arrPoints[1]->x, PDO::PARAM_INT);
		$qry->bindValue('y2', $this->arrPoints[1]->y, PDO::PARAM_INT);
		$qry->bindValue('x3', $this->arrPoints[2]->x, PDO::PARAM_INT);
		$qry->bindValue('y3', $this->arrPoints[2]->y, PDO::PARAM_INT);
		$qry->bindValue('x4', $this->arrPoints[3]->x, PDO::PARAM_INT);
		$qry->bindValue('y4', $this->arrPoints[3]->y, PDO::PARAM_INT);
		$qry->execute();
		$qry->closeCursor();

	}



}
