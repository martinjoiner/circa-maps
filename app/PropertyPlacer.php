<?php

namespace App;

use App\Math;

/**
 * This class is a map but with only the routes and properties in proximity to given coordinates
 */
class PropertyPlacer extends MapSection {


	/**
	 * Places a property on the map if it's points do not collide
	 *
	 * @param {Point} $point
	 * 
	 * @return {array} Contains: 'arrPath', {boolean} 'success'
	 */
	public function placeProperty( Point $point ){

		$startTime = microtime(true);

		$arrResult = [ 	'arrPath' => null, 
						'success' => false,
						'message' => ''
					];

		// Get the nearest route
		$arrNearestRouteResult = $this->nearestRoute( $point );
		$closestPointOnRoute = $arrNearestRouteResult['closestPointOnRoute'];


		if( is_null($closestPointOnRoute['distanceToPointResult']) || $closestPointOnRoute['distanceToPointResult'] > 50 ){
			$arrResult['message'] = 'Closest point was too far';
		 	return $arrResult;
		}

		// Generate 4 points based on supplied point

		$arrFrontLeftPoint = Math::pointDistanceBetweenPoints( $closestPointOnRoute['arrPointResult'], $point, 8); 
		$arrFrontRightPoint = Math::ninetyDeg( $point, $arrFrontLeftPoint ); 
		$arrRearRightPoint = Math::ninetyDeg( $arrFrontLeftPoint, $arrFrontRightPoint ); 

		$point = $point->randomVary( 5 );
		$arrRearRightPoint = $arrRearRightPoint->randomVary( 5 );

		$arrPoints = [ $arrFrontLeftPoint, $arrFrontRightPoint, $arrRearRightPoint, $point ];

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

		return $arrResult;
	}




}
