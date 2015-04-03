<?php

Class CoordinateGeometry extends Math{

	/**
	 Calculates the equation of the line segment as if it continues forever
	 y = mx + b
	 Returns array with keys 'm', 'b', 'x', 'equation' and 'isVertical'
	*/
	function equationOfLine( $arrPointA, $arrPointB ){

		// Set up the return array with all possible elements
		$arrReturn = array( 'm'=>null, 'b'=>null, 'x'=>null, 'equation'=>'y = mx + b', 'isVertical'=>false );

		// Arrange the points so that point A is on the left
		parent::orientSoLeftmostIsA( $arrPointA, $arrPointB );

		// If the line is horizontal the equation is easy! 
		if( $arrPointA['y'] === $arrPointB['y'] ){
			$arrReturn['m'] = 0;
			$arrReturn['b'] = $arrPointA['y'];
			return $arrReturn;
		}

		// If the line is vertical the equation is easy!
		if( $arrPointA['x'] === $arrPointB['x'] ){
			$arrReturn['isVertical'] = true;
			$arrReturn['equation'] = 'x = ' . $arrPointA['x'];
			$arrReturn['x'] = $arrPointA['x'];
			return $arrReturn;
		}

		// Calculate theta 
		$oppositeToTheta = $arrPointB['y'] - $arrPointA['y'];

		$adjacentToTheta = $arrPointB['x'] - $arrPointA['x'];

		$tanTheta = $oppositeToTheta / $adjacentToTheta;

		$radTheta = atan( $tanTheta );
		
		$degTheta = rad2deg( $radTheta );

		// Now project a triangle from 0,0 and use opposite side to calculate m and b
		$oppositeToDelta = tan( deg2rad( $degTheta ) ) * $arrPointA['x'];

		$arrReturn['b'] = $arrPointA['y'] - round( $oppositeToDelta, 2);

		$arrReturn['m'] = $oppositeToDelta / $arrPointA['x'];

		return $arrReturn;

	}


}
