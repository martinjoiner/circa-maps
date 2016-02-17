<?php

abstract class Map{

	protected $id = 0;
	protected $name = ''; 

	/**
	 * The total width and height of the whole map
	 */
	protected $width = 0;
	protected $height = 0;

	/**
	 * The upper limit for the width of a property
	 */
	protected $maxPropertyWidth = 100;

	protected $arrRoutes =[];

	protected $arrProperties = [];




	/**
	 * Extracts the basic details for the map and sets the class variables
	 */
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
	 * @param 
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

			$arrPoints[] = array( 'x'=>intval($thisResult['x']), 'y'=>intval($thisResult['y']) );
			
		}
		if( $curID != 0 ){
			$this->makePathType( $curID, $arrPoints, $pathType );
		}
	}



	/**
	 * 
	 *
	 * @param {integer} $id
	 * @param {array} $arrPoints
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




	public function limitXToBoundaries( $x ){
		if( $x < 0 ){
			return 0;
		} else if( $x > $this->width ){
			return $this->width;
		}
		return $x;
	}




	public function limitYToBoundaries( $y ){
		if( $y < 0 ){
			return 0;
		} else if( $y > $this->height ){
			return $this->height;
		}
		return $y;
	}




	/** 
	 * Returns an array of arrays representing fronts
	 */
	public function getProperties(){
		$arrFronts = array();
		foreach( $this->arrProperties as $thisProperty ){
			$arrFronts[] = $thisProperty->getPath();
		}
		return $arrFronts;
	}




	/** 
	 * Returns an array of arrays representing fronts
	 */
	public function getPropertyFronts(){
		$arrFronts = array();
		foreach( $this->arrProperties as $thisProperty ){
			$arrFronts[] = $thisProperty->getFront();
		}
		return $arrFronts;
	}




	/** 
	 * Returns an array of arrays representing properties
	 */
	public function getRoutes(){
		$arrFronts = array();
		foreach( $this->arrRoutes as $thisRoute ){
			$arrFronts[] = $thisRoute->getPath();
		}
		return $arrFronts;
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
	 * Tests all routes on the Map against a provided object of class Property
	 * return true if any sides of the property intersect a segment of the route
	 * (useful when checking for improved version of property)
	 *
	 * @param {Property} 
	 *
	 * @return {boolean}
	 */
	protected function isCollisionWithMapRoutes( Property $objPropertySubject ){
		
		$arrAllSegments = [];

		$arrCenterData = $objPropertySubject->getCenterData();
		$arrCenterPoint = $arrCenterData['arrCenterPoint'];

		// Get all the route segments within range
		foreach( $this->arrRoutes as $objThisRoute ){
			// Append this route's segments to the total array
			$arrAllSegments = array_merge( $arrAllSegments, $objThisRoute->getSegmentsWithinRange( $arrCenterPoint, 100 ) );
		}

		$cntSegments = sizeof($arrAllSegments);

		$objCoordinateGeometry = new CoordinateGeometry();

		// Test each of the properties' sides against all the nearby route segments 
		for( $i = 0; $i < 4; $i++ ){
			$thisSide = $objPropertySubject->getSide($i);
			for( $s = 0; $s < $cntSegments; $s++ ){
				if( $objCoordinateGeometry->doSegmentsIntersect( $thisSide, $arrAllSegments[$s] ) ){
					return true;
				}
			}
		}

		return false;
	}

}

