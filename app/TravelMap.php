<?php

namespace App;

use PDO;
use App\Point;
use App\Route;
use App\Math;

/**
 * A kind of "pathfinder" class, a satnav for the map
 */
class TravelMap extends MapComplete {

	/** array to store possible steps to get between origin and target */
	private $possibleSteps = [];

	/**
	 * If possible, finds the shortest sequence of steps to get from origin to target
	 *
	 * @param {App\Point} $originPoint
	 * @param {App\Point} $targetPoint - The target destination
	 *
	 * @return array
	 */
	public function travelShortest( Point $originPoint, Point $targetPoint )
	{
		// Get nearest point on route for each point. 
		$nearestRouteToOrigin = @$this->nearestRoute($originPoint);
		$nearestRouteToTarget = @$this->nearestRoute($targetPoint);

		$pointOnOriginsNearestRoute = $nearestRouteToOrigin['closestPointOnRoute']['arrPointResult'];
		$pointOnTargetsNearestRoute = $nearestRouteToTarget['closestPointOnRoute']['arrPointResult'];

		// If same route, easy! 
		if( $nearestRouteToOrigin['route']->getId() === $nearestRouteToTarget['route']->getId() ){
			$result = [ 
				'message' => 'Points on same route',
				'possible' => true,
				'steps' => [ $originPoint, $pointOnOriginsNearestRoute, $pointOnTargetsNearestRoute, $targetPoint ]
			];
		} else {

			// They are on different route
			$result = [ 
				'message' => 'Points on different routes',
				'possible' => false // Assume not possible until proven steps found
			];

			// Steps will always start with origin and it's point on nearest route
			$steps = [ $originPoint, $pointOnOriginsNearestRoute ];

			// Reset $this->possibleSteps
			$possibleSteps = [];

			// Call recursive function to populate $this->possibleSteps
			$this->findJunctionsToRoute( $nearestRouteToOrigin['route'], $nearestRouteToTarget['route'], $steps );

			if( count($this->possibleSteps) ){
				// Find the shortest
				$shortestDistance = INF;
				foreach( $this->possibleSteps as $possible ){
					$possible = array_merge( $possible, [$pointOnTargetsNearestRoute, $targetPoint] );
					$thisDistance = $this->totalStepsDistance( $possible );
					if( $thisDistance < $shortestDistance ){
						$result['steps'] = $possible;
						$result['possible'] = true;
						$shortestDistance = $thisDistance;
					}
				}
			}

		}

		if( $result['possible'] ){
			$result['crowDistance'] = Math::distanceBetween($originPoint, $targetPoint);
			$result['stepsDistance'] = $this->totalStepsDistance( $result['steps'] );
		}

		return $result;
	}




	/**
	 * Recursive function to find valid combinations of steps (junctions) to get between origin and target 
	 *
	 * @param App\Route $onRoute
	 * @param App\Route $targetRoute
	 * @param array $steps
	 * @param array $visitedRoutes An array of routes we have visited, used to stop never-ending circles
	 */
	private function findJunctionsToRoute( Route $onRoute, Route $targetRoute, $steps, $visitedRoutes = [] )
	{

		// The ideal junction would be one between the route we're 
		// on and the target route, but it may not exist!
		$finalJunctionKey = Junction::makeKey($onRoute, $targetRoute);

		$junctions = $onRoute->junctionsWithRoutes( $this->arrRoutes );

		// Iterate over junctions, calling this function on routes we haven't visited yet
		foreach( $junctions as $junction ){
			// Add this junction as a step
			$theseSteps = array_merge( $steps, [$junction->getPoint()] );
			$startRoute = $junction->getOtherRoute($onRoute);

			if( $junction->hasRoute($targetRoute) ){
				// We've found a final junction
				$this->possibleSteps[] = array_merge( $theseSteps, [$junction->getPoint()] );
			} else {
 			
				$visitedRoutes = array_merge( $visitedRoutes, [$onRoute->getId()] );
	 			if( !in_array($startRoute->getId(), $visitedRoutes) ){
					$this->findJunctionsToRoute($startRoute, $targetRoute, $theseSteps, $visitedRoutes);
				}
			}
		}

	}




	/**
	 * @param {array} of App\Points
	 *
	 * @return {number} Total distance
	 */
	private function totalStepsDistance( array $points )
	{
		$distance = 0;
		$iLimit = count($points) - 1;
		for( $i = 0; $i < $iLimit; $i++ ){
			$distance += Math::distanceBetween($points[$i], $points[$i+1]);
		}
		return $distance;
	}

}

