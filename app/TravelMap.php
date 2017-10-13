<?php

namespace App;

use PDO;
use App\Point;
use App\Route;

/**
 * 
 */
class TravelMap extends MapComplete {

	/**
	 * 
	 *
	 * @param {App\Point} $pointA
	 * @param {App\Point} $pointB
	 *
	 * @return 
	 */
	public function travelShortest( Point $pointA, Point $pointB )
	{
		// Get nearest point on route for each point. 
		$nearestRouteA = $this->nearestRoute($pointA);
		$nearestRouteB = $this->nearestRoute($pointB);

		$pointOnRouteA = $nearestRouteA['closestPointOnRoute']['arrPointResult'];

		// If same route, easy! 
		if( $nearestRouteA['route']->getId() === $nearestRouteB['route']->getId() ){
			return [ 'message' => 'Same Route' ];
		} else {

			// They are on different route

			$result = [ 'message' => 'Different Routes' ];

			$result['steps'] = $this->findJunctionsToRoute( $nearestRouteA['route'], $nearestRouteB['route'] );

			return $result;
		}

	}



	/**
	 * Recursive function to find junction
	 */
	private function findJunctionsToRoute( Route $onRoute, Route $targetRoute )
	{
		// The ideal junction would be one between the route we're 
		// on and the target route, but it may not exist!
		$soughtJunctionKey = Junctions::makeKey($onRoute, $targetRoute);

		$junctions = $onRoute->junctionsWithRoutes( $this->arrRoutes );

		if( isSet($junctions[$soughtJunctionKey]) ){
			// We've found a valid step
			return null;
		} 

		// Iterate over junctions, calling this function on routes we haven't found yet
		//{
			// If this is a junction with the other route? 
			
		//}
		return null;
	}

}

