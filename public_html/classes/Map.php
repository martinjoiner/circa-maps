<?php

/**
 * Map abstract class is inherited by instantiations of MapComplete, MapInitCrossRoads or MapSection
 */
abstract class Map{

	/** {integer} Database ID of the map */
	protected $id = 0;

	/** {string} Name of the map */
	protected $name = ''; 

	/** {integer} The total width of the whole map */
	protected $width = 0;

	/** {integer} The total height of the whole map */
	protected $height = 0;

	/** {integer} The upper limit for the width of a property */
	protected $maxPropertyWidth = 100;

	/** {array} All the routes on this map */
	protected $arrRoutes =[];

	/** {array} All the properties on this MapComplete or MapSection */
	protected $arrProperties = [];




	/** Extracts the basic details for the map and sets the class variables */
	protected function extractMapFromDB(){

		include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

		$qry = $db->prepare("	SELECT 	`name`, `width`, `height` 
								FROM 	`map`
								WHERE 	`id` = :mapID 
							");
		$qry->bindValue('mapID', $this->id, PDO::PARAM_INT);
		$qry->execute();
		$rslt = $qry->fetchAll(PDO::FETCH_ASSOC);
		$qry->closeCursor();

		$thisResult = $rslt[0];

		$this->name 		= $thisResult['name'];
		$this->width 		= intval($thisResult['width']);
		$this->height 		= intval($thisResult['height']);

	}



	/** 
	 * Takes a db query result and loops through creating the route objects in the arrRoutes array
	 *
	 * @param {array} $rslt
	 * @param {string} $pathType 
	 */
	protected function processDBResult( $rslt, $pathType = 'ROUTE' ){

		$curID = 0;
		foreach( $rslt as $thisResult ){

			if( $curID != $thisResult['id'] ){

				if( $curID != 0 ){
					$this->makePathType( $curID, $arrPoints, $pathType );
				}

				// Reset the variables
				$arrPoints = array();
				$curID = intval($thisResult['id']);
			}

			$arrPoints[] = new Point( $thisResult['x'], $thisResult['y'] );
			
		}
		if( $curID != 0 ){
			$this->makePathType( $curID, $arrPoints, $pathType );
		}
	}




	/** MapComplete and MapSection both extract data differently */
	abstract protected function extractRoutesFromDB();




	/**
	 * 
	 *
	 * @param {integer} $id
	 * @param {array} $arrPoints Array of instances of Point class
	 * @param {string} $pathType
	 */
	private function makePathType( $id, $arrPoints, $pathType ){
		if( $pathType == 'ROUTE' ){
			$this->arrRoutes[] = new Route( $id, $arrPoints );
		} else if ( $pathType == 'PROPERTY' ){
			$this->arrProperties[] = new Property( $arrPoints, $this->id, $id );
		}
	}




	/**
	 * IN DEV
	 */
	public function placeRandPath(){
		// Know the minimum area of footprint you want to build on
		$desArea = 80 * 80; // Desired area of property

		// Walk away from AAP, until in unoccupied space, set point1.
		// Try stepping 90 deg to AAP.
		// Try stepping closer to AAP.
		// Repeat previous 2 steps until both are failing, set point2.
		// Clone these 2 points and translate them away from their roots by half the distance between them
		// If point3 is in occupied space, move it closer toward point4 and record the distance required to get in free space.
		// Attempt to move point 4 that same distance away, monitoring area, when desired area is reached. Declare the property.

	}




	/**
	 * Takes a coordinate value, if it's outside the map boundaries it sets it to inside
	 *
	 * @param {integer} $x
	 *
	 * @return {integer} Coordinate value definitely inside map boundaries
	 */
	public function limitXToBoundaries( $x ){
		if( $x < 0 ){
			return 0;
		} else if( $x > $this->width ){
			return $this->width;
		}
		return $x;
	}




	/**
	 * Takes a coordinate value, if it's outside the map boundaries it sets it to inside
	 *
	 * @param {integer} $y
	 *
	 * @return {integer} Coordinate value definitely inside map boundaries
	 */
	public function limitYToBoundaries( $y ){
		if( $y < 0 ){
			return 0;
		} else if( $y > $this->height ){
			return $this->height;
		}
		return $y;
	}




	/** 
	 * Returns an array of arrays representing properties
	 *
	 * @return {array} 
	 */
	public function getProperties(){
		$arrProperties = [];
		foreach( $this->arrProperties as $thisProperty ){
			$arrProperties[] = $thisProperty->getPath();
		}
		return $arrProperties;
	}




	/** 
	 * Returns an array of arrays representing fronts
	 *
	 * @return {array} 
	 */
	public function getPropertyFronts(){
		$arrFronts = [];
		foreach( $this->arrProperties as $thisProperty ){
			$arrFronts[] = $thisProperty->getFront();
		}
		return $arrFronts;
	}




	/** 
	 * Returns an array of arrays representing properties
	 *
	 * @return {array} 
	 */
	public function getRoutes(){
		$arrFronts = array();
		foreach( $this->arrRoutes as $thisRoute ){
			$arrFronts[] = $thisRoute->getPath();
		}
		return $arrFronts;
	}




	/**
	 * Takes a co-ordinate and returns true if there is a property sitting on that point or a route intersecting
	 *
	 * @param {integer} $x
	 * @param {integer} $y
	 *
	 * @return {array} Contains: 'cntProperties', 'cntRoutes', 'isOccupied', 'occupationType', 'message'
	 */
	public function isOccupied( $x, $y ){

		$arrResult = [ 	'cntProperties' => sizeof($this->arrProperties), 
						'cntRoutes' => sizeof($this->arrRoutes), 
						'isOccupied' => false, 
						'occupationType' => null,
						'message' => '' 
					];

		$point = new Point( $x, $y );

		// Iterate over properties, checking if point is inside it 
		foreach( $this->arrProperties as $pointer => $thisProperty ){

			$points_polygon = count($thisProperty->arrPoints);  // number vertices - zero-based array

			if( Math::isInPolygon($points_polygon, $thisProperty->arrVerticesX, $thisProperty->arrVerticesY, $point) ){
				$arrResult['isOccupied'] = true;
				$arrResult['occupationType'] = 'PROPERTY';
				$arrResult['arrPropertiesPointer'] = $pointer;
				$arrResult['propertyInfo'] = $thisProperty->getInfo();
				$arrResult['message'] .= $point->coordSentence() . ' is inside property ID ' . $thisProperty->id . ' (area: ' . $arrResult['propertyInfo']['arrAreaData']['area'] . '). ';
			} 

		}

		// Fetch the nearest Route
		$nearestRouteResult = $this->nearestRoute( $point );
		if( $nearestRouteResult['distanceToClosestPointOnRoute'] > 0 && $nearestRouteResult['distanceToClosestPointOnRoute'] < 10 ){
			$arrResult['isOccupied'] = true;
			$arrResult['occupationType'] = 'ROUTE';
			$arrResult['message'] .= 'Point is ' . $nearestRouteResult['distanceToClosestPointOnRoute'] . ' units from a route. ';
		}

		return $arrResult;

	}




	/**
	 * Returns an array of variables describing the nearest route, the closest point on that route, and the distance to that point
	 *
	 * @param {Point} $point
	 *
	 * @return {array} Contains: closestPointOnRoute, cntRoutesChecked, closestDistance (closestPointOnRoute, distanceToClosestPointOnRoute, cntRoutesChecked)
	 */
	public function nearestRoute( Point $point ){

		$arrResult = [ 	'closestPointOnRoute' => null, 
						'cntRoutesChecked' => 0, 
						'closestDistance' => INF
					];

		$nearestRoute = array();
		$cntRoutesChecked = 0;

		// Iterate over all the routes
		foreach( $this->arrRoutes as $thisRoute ){
			$thisResult = $thisRoute->gimme2NearestPoints( $point );
			if( $arrResult['closestDistance'] > $thisResult['closestDistance'] ){
				$arrResult['closestDistance'] = $thisResult['closestDistance'];
				$nearestRoute = $thisResult;
			}
			$cntRoutesChecked++;
		}

		if( $cntRoutesChecked ){
			$closestPointBetween2 = Math::closestPointBetween2( $point, $nearestRoute['top2NearestPoints'][0], $nearestRoute['top2NearestPoints'][1] );
			$arrResult['closestPointOnRoute'] = $closestPointBetween2;
			$arrResult['distanceToClosestPointOnRoute'] = Math::distanceBetween( $point, $closestPointBetween2['arrPointResult'] );
			$arrResult['cntRoutesChecked'] = $cntRoutesChecked;
		}

		return $arrResult;
	}




	/**
	 * Returns all the points where routes intersect
	 *
	 * @return {array} of Points
	 */
	public function junctions(){

		$arrResult = [];

		// Iterate over all the routes
		foreach( $this->arrRoutes as $routeA ){

			// Iterate over all the routes apart from the last one because by the nature of grid iteration that one will have already been checked
			$iLimit = count($this->arrRoutes) - 1;
			for( $i = 0; $i < $iLimit; $i++ ){

				$routeB = $this->arrRoutes[$i];

				// Don't bother to check if a route intersects with itself
				if( $routeA->getId() != $routeB->getId() ){

					$arrResult = array_merge( $arrResult, $routeA->intersectionsWithRoute( $routeB ) );

				}

			}

		}

		return $arrResult;
	}




	/** 
	 * Tests all properties on the Map against a provided object of class Property looking for collisions
	 * (useful when checking for improved version of property)
	 *
	 * @param {Property} $objPropertySubject
	 *
	 * @return {boolean}
	 */
	protected function isCollisionWithMapProperties( Property $objPropertySubject ){
		
		// Initialise an object of class PropertyCollision 
		$objPropertyCollision = new PropertyCollision();
		
		// Check for collision with all properties on map
		foreach( $this->arrProperties as $objThisProperty ){
			// Only test for collision if the property's IDs are different
			if( $objThisProperty->id != $objPropertySubject->id ){

				if( $objPropertyCollision->isCollision( $objPropertySubject, $objThisProperty ) ){
					return true;
				}
			}
		}

		return false;
	}




	/** 
	 * Gets all segments of all routes within range 
	 *
	 * @param {Point} $centerPoint
	 * @param {integer} $searchRadius
	 *
	 * @return {array}
	 */
	public function getRouteSegmentsWithinRange( Point $centerPoint, $searchRadius = 100 ){
		$arrAllSegments = [];

		// Get all the route segments within range
		foreach( $this->arrRoutes as $thisRoute ){
			// Append this route's segments to the total array
			$arrAllSegments = array_merge( $arrAllSegments, $thisRoute->getSegmentsWithinRange( $centerPoint, $searchRadius ) );
		}

		return $arrAllSegments;
	}




	/** 
	 * Tests all routes on the Map against a provided object of class Property
	 * return true if any sides of the property intersect a segment of the route
	 * (useful when checking for improved version of property)
	 *
	 * @param {Property} 
	 *
	 * @return {boolean}
	 */
	protected function isCollisionWithMapRoutes( Property $objPropertySubject ){
		
		$arrCenterData = $objPropertySubject->getCenterData();
		$centerPoint = $arrCenterData['centerPoint'];
		$searchRadius = $arrCenterData['farthestRadius'] * 3;

		$arrAllSegments = $this->getRouteSegmentsWithinRange( $centerPoint, $searchRadius );

		$cntSegments = count($arrAllSegments);

		// Test each of the properties' sides against all the nearby route segments 
		for( $i = 0; $i < 4; $i++ ){
			$thisSide = $objPropertySubject->getSide($i);
			for( $s = 0; $s < $cntSegments; $s++ ){
				if( CoordinateGeometry::doSegmentsIntersect( $thisSide, $arrAllSegments[$s] ) ){
					return true;
				}
			}
		}

		return false;
	}

}

