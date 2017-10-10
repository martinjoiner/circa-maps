<?php

namespace App;

use App\Math;

/**
 * A checker to see if 2 properties overlap/collide
 */
class PropertyCollision{


	/**
	 * Tests 2 properties to see if they are overlapping 
	 * http://content.gpwiki.org/index.php/Polygon_Collision 
	 *
	 * @param {Property} $property1
	 * @param {Property} $property2
	 *
	 * @return {boolean} Was a collision detected?
	 */
	public function isCollision( Property $property1, Property $property2 ){

		$arrProperty1Data = $property1->getCenterData();
		$arrProperty2Data = $property2->getCenterData();

		$distanceBetweenCentres = Math::distanceBetween( $arrProperty1Data['centerPoint'], $arrProperty2Data['centerPoint'] );
		$sumOfFarthestRadii = $arrProperty1Data['farthestRadius'] + $arrProperty2Data['farthestRadius'];

		// Firstly: If distance between mid points is more than sum of maximum radius there definitely cannot be a collision (fastest)
		if( $distanceBetweenCentres > $sumOfFarthestRadii ){
			return false;
		}

		// Next, check if distance is less than either path's nearestRadius
		// If so there definitely *is* a collision! This is good for checking paths that are exactly on top of each other.
		if( $distanceBetweenCentres < $arrProperty1Data['nearestRadius'] || $distanceBetweenCentres < $arrProperty2Data['nearestRadius'] ){
			//console.warn('polyCollision(): Failed at stage 2');
			return true;
		}

		// Check if any of $property1's points are inside $property2
		foreach( $property1->arrPoints as $thisPoint ){
			if( $property2->coversPoint( $thisPoint ) ){
				return true;
			}
		}

		// Now check if any of $property2's points are inside $property1
		foreach( $property2->arrPoints as $thisPoint ){
			if( $property1->coversPoint( $thisPoint ) ){
				return true;
			}
		}

		// TODO: Finally use the more expensive method of separating Axis which will account for very rare cases when mid sections overlap

		// If we've reached this far without detecting a collision, return false
		return false;
	}


}
