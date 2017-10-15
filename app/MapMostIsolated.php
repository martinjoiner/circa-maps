<?php

namespace App;

/**
 * Class for finding the most isolated point on a map
 * Note: Should this be written as a class that doesn't extend
 */
class MapMostIsolated extends MapComplete {


	/**
	 * Searches the map for the most isolated point (eg, Furthest from a route) 
	 *
	 * @return {array} Contains 'point', 'closestDistance'
	 */
	public function findMostIsolatedPoint(){

        $result = [ 
            'point' => null,
            'closestDistance' => 0
        ];

		/** {integer} Number of units to leap on each iteration */
		$leap = 50;

		// Iterate over X in 50 unit leaps
		for( $x = 0; $x < $this->width; $x = $x + $leap ){

			// Iterate over Y in 50 unit leaps 
			for( $y = 0; $y < $this->height; $y = $y + $leap ){

				$point = new Point($x, $y);

				$nearestRouteRslt = $this->nearestRoute( $point );

                if( $result['closestDistance'] < $nearestRouteRslt['closestDistance'] ){
                    $result['closestDistance'] = $nearestRouteRslt['closestDistance'];
                    $result['point'] = $point;
                }

			}

		}

        return $result;
	}


}

