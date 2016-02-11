<?php

/**
 This class is a map but with only the routes and properties in proximity to given coordinates
*/
class PropertyPlacer extends MapSection{


	/**
	 Places a property on the map if it's points do not collide
	*/
	public function placeProperty( $x, $y ){

		$startTime = microtime(true);

		$arrResult = array( 'arrPath'=>null, 'success'=>false );

		$objMath = new Math();

		// Get the nearest route
		$arrNearestRouteResult = $this->nearestRoute( $x, $y );
		$closestPointOnRoute = $arrNearestRouteResult['closestPointOnRoute'];


		if( is_null($closestPointOnRoute['distanceToPointResult']) || $closestPointOnRoute['distanceToPointResult'] > 50 ){
		 	return $arrResult;
		}

		// Generate 4 points based on supplied x and y
		$arrRearLeftPoint = array( 'x'=>$x, 'y'=>$y ); 
		$arrFrontLeftPoint = $objMath->pointDistanceBetweenPoints( $closestPointOnRoute['arrPointResult'], $arrRearLeftPoint, 8); 
		$arrFrontRightPoint = $objMath->ninetyDeg( $arrRearLeftPoint, $arrFrontLeftPoint ); 
		$arrRearRightPoint = $objMath->ninetyDeg( $arrFrontLeftPoint, $arrFrontRightPoint ); 

		$arrRearLeftPoint = $objMath->randomVaryPoint( $arrRearLeftPoint, 5 );
		$arrRearRightPoint = $objMath->randomVaryPoint( $arrRearRightPoint, 5 );

		$arrPoints = array( $arrFrontLeftPoint, $arrFrontRightPoint, $arrRearRightPoint, $arrRearLeftPoint );

		// Initialise an object to represent our proposed property 
		$objProposedProperty = new Property( $arrPoints, $this->id );

		// Check for collision with all properties on map
		if( parent::isCollisionWithMapProperties( $objProposedProperty ) ){
			$isValid = false;
		} else {
			$isValid = true;
		}

		if( $isValid ){
			// Check if property meets standards
			$arrProposedPropertyInfo = $objProposedProperty->getInfo();
			if( !$arrProposedPropertyInfo['isStandard'] ){
				$isValid = false;
			}
		}

		if( $isValid ){

			// If no collisions init a Property object
			$objPropertyNew = new Property( $arrPoints, $this->id );

			// Call the saveInDB method of the newly created Property 
			$objPropertyNew->saveInDB();

			$this->arrProperties[] = $objPropertyNew;

			$arrResult['success'] = true;
			$arrResult['arrPath'] = $this->arrProperties[ sizeof($this->arrProperties)-1 ]->getPath();
		}

		$arrResult['executionTime'] = microtime(true) - $startTime;

		// Return result and arrPath
		return $arrResult;
	}




}
