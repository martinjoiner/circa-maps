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
		parent::orientSoLeftmostIsFirst( $arrPointA, $arrPointB );

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




	/**
	 Returns a simple true or false from the more complicated lineSegmentIntersectionPoint() method
	*/
	function doSegmentsIntersect( $lineSegmentA, $lineSegmentB ){
		$result = self::lineSegmentIntersectionPoint( $lineSegmentA, $lineSegmentB );
		return $result['intersectionOnSegment'];
	}
	



	/**
	 Returns the point at which the lines of 2 line segments intersect
	 and if whether that point is on the segments
	 TODO: This does not yet handle intersections with vertical lines
	*/
	function lineSegmentIntersectionPoint( $lineSegmentA, $lineSegmentB ){

		// Arrange the points in both segments so that the first point is on the left
		parent::orientSoLeftmostIsFirst( $lineSegmentA[0], $lineSegmentA[1] );
		parent::orientSoLeftmostIsFirst( $lineSegmentB[0], $lineSegmentB[1] );

		// Get the equations of both line segments
		$equationOfLineA = $this->equationOfLine( $lineSegmentA[0], $lineSegmentA[1] );
		$equationOfLineB = $this->equationOfLine( $lineSegmentB[0], $lineSegmentB[1] );

		// mx + b = mx + b --> mx - mx = b - b
		$rightSideVal = $equationOfLineB['b'] - $equationOfLineA['b'];

		// mx - mx = $rightSideVal --> (m-m)x = $rightSideVal --> x = $rightSideVal / (m-m)
		$x = $rightSideVal / ( $equationOfLineA['m'] - $equationOfLineB['m'] );

		// Now we have x, use A's equation to calculate y
		$y = ( $equationOfLineA['m'] * $x ) + $equationOfLineA['b'];

		// Check if the intersection point is within one of the line segments x limits
		if( $x > $lineSegmentA[0]['x'] && $x < $lineSegmentA[1]['x'] ){
			$intersectionOnSegment = true;
		} else {
			$intersectionOnSegment = false;
		}

		return array( 'x'=>$x, 'y'=>$y, 'intersectionOnSegment'=>$intersectionOnSegment );

	}



}
