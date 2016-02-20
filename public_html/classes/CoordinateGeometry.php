<?php

Class CoordinateGeometry extends Math{

	/**
	 * Calculates the equation of the line segment as if it continues forever
	 * y = mx + b
	 *
	 * @param {Point} $pointA
	 * @param {Point} $pointB
	 *
	 * @return {array} Containing 'm', 'b', 'x', 'equation', 'isVertical' and 'isHorizontal'
	 */
	private function equationOfLine( Point $pointA, Point $pointB ){

		// Set up the return array with all possible elements
		$arrReturn = [ 	'm' => null, 
						'b' => null, 
						'x' => null, 
						'equation' => 'y = mx + b', // Equation in slope/intercept form
						'isVertical' => false, 
						'isHorizontal' => false 
					];

		// Arrange the points so that point A is on the left
		parent::orientSoLeftmostIsFirst( $pointA, $pointB );

		// If the line is horizontal the equation is easy! 
		if( $pointA->y == $pointB->y ){
			$arrReturn['isHorizontal'] = true;
			$arrReturn['m'] = 0;
			$arrReturn['b'] = $pointA->y;
			$arrReturn['equation'] = 'y = ' . $pointA->y;
			return $arrReturn;
		}

		// If the line is vertical the equation is easy!
		if( $pointA->x == $pointB->x ){
			$arrReturn['isVertical'] = true;
			$arrReturn['x'] = $pointA->x;
			$arrReturn['equation'] = 'x = ' . $pointA->x;
			return $arrReturn;
		}

		// Calculate theta 
		$oppositeToTheta = $pointB->y - $pointA->y;

		$adjacentToTheta = $pointB->x - $pointA->x;

		$tanTheta = $oppositeToTheta / $adjacentToTheta;

		$radTheta = atan( $tanTheta );
		
		$degTheta = rad2deg( $radTheta );

		// Now project a triangle from 0,0 and use opposite side to calculate m and b
		$oppositeToDelta = tan( deg2rad( $degTheta ) ) * $pointA->x;

		$arrReturn['b'] = $pointA->y - round( $oppositeToDelta, 2);

		if( $pointA->x === 0 ){

			// Use the difference between points to calculate m
			$arrReturn['m'] = $oppositeToTheta / $adjacentToTheta;

		} else {

			$arrReturn['m'] = $oppositeToDelta / $pointA->x;

		}

		$arrReturn['equation'] = 'y = ' . $arrReturn['m'] . 'x + ' . $arrReturn['b'];

		return $arrReturn;
	}




	/**
	 * Returns true if intersection from lineSegmentIntersectionPoint() occurs on first segment
	 *
	 * @param {array} $lineSegmentA Containing 2 instances of Point class
	 * @param {array} $lineSegmentB Containing 2 instances of Point class
	 *
	 * @return {boolean} 
	 */
	public static function doSegmentsIntersect( $lineSegmentA, $lineSegmentB ){
		$result = self::lineSegmentIntersectionPoint( $lineSegmentA, $lineSegmentB );
		if( $result['intersectionOnSegment'] == 'BOTH' ){
			return true;
		} else {
			return false;
		}
	}
	



	/**
	 * Returns the theoretical point at which the equations of the lines of 2 line segments intersect
	 * and if whether that theoretical intersection point is actually on the first segment, second, both or neither
	 *
	 * @param {array} $lineSegmentA Containing 2 instances of Point class
	 * @param {array} $lineSegmentB Containing 2 instances of Point class
	 *
	 * @return {array} Contains 'point', {string} 'intersectionOnSegment' ('NEITHER','A','B','BOTH'), 'linesAreParallel'
	 */
	public static function lineSegmentIntersectionPoint( $lineSegmentA, $lineSegmentB ){

		$arrReturn = [ 	'point' => null, 
						'intersectionOnSegment' => 'NEITHER',
						'linesAreParallel' => false 
					];

		// Arrange the points in both segments so that the first point is on the left
		parent::orientSoLeftmostIsFirst( $lineSegmentA[0], $lineSegmentA[1] );
		parent::orientSoLeftmostIsFirst( $lineSegmentB[0], $lineSegmentB[1] );

		// Get the equations of both line segments
		$equationOfLineA = static::equationOfLine( $lineSegmentA[0], $lineSegmentA[1] );
		$equationOfLineB = static::equationOfLine( $lineSegmentB[0], $lineSegmentB[1] );

		// If the slopiness of both lines is equal or both m values are null, they will never intersect
		if( $equationOfLineA['m'] === $equationOfLineB['m'] ){ 
			$arrReturn['linesAreParallel'] = true;
			return $arrReturn;
		}

		// If one of the lines is vertical the equation is different
		if( $equationOfLineA['isVertical'] != $equationOfLineB['isVertical'] ){

			if( $equationOfLineA['isVertical'] ){
				$x = $equationOfLineA['x'];
				$verticalSegmentLetter = 'A';
				$verticalSegment = $lineSegmentA;
				$nonVertEquation = $equationOfLineB;
				
			} else {
				$x = $equationOfLineB['x'];
				$verticalSegmentLetter = 'B';
				$verticalSegment = $lineSegmentB;
				$nonVertEquation = $equationOfLineA;
			}

			// y = mx + b 
			$y = ( $nonVertEquation['m'] * $x ) + $nonVertEquation['b'];
			$y = round( $y, 2 );

			// What's this nonsense! It removes -0 which can be returned by line above
			if( $y == 0 ){
				$y = 0;
			}

			// Check if the intersection point is within the vertical segments y limits
			if( $y >= min( $verticalSegment[0]->y, $verticalSegment[1]->y ) && $y <= max( $verticalSegment[0]->y, $verticalSegment[1]->y ) ){
				$arrReturn['intersectionOnSegment'] = $verticalSegmentLetter;
			} 

		} else {

			// mx + b = mx + b --> mx - mx = b - b
			$rightSideVal = $equationOfLineB['b'] - $equationOfLineA['b'];

			// mx - mx = $rightSideVal --> (m-m)x = $rightSideVal --> x = $rightSideVal / (m-m)
			$x = $rightSideVal / ( $equationOfLineA['m'] - $equationOfLineB['m'] );

			// Now we have x, use A's equation to calculate y
			$y = ( $equationOfLineA['m'] * $x ) + $equationOfLineA['b'];

			// Check if the intersection point is within segmentA's x limits
			if( $x >= $lineSegmentA[0]->x && $x <= $lineSegmentA[1]->x ){
				$arrReturn['intersectionOnSegment'] = 'A';

				// Also check if the intersection point is within segmentB's x limits
				if( $x >= $lineSegmentB[0]->x && $x <= $lineSegmentB[1]->x ){
					$arrReturn['intersectionOnSegment'] = 'BOTH';
				}

			} else {

				// Check if the intersection point is within segmentB's x limits
				if( $x >= $lineSegmentB[0]->x && $x <= $lineSegmentB[1]->x ){
					$arrReturn['intersectionOnSegment'] = 'B';
				}

			}

		}

		$arrReturn['point'] = new Point( $x, $y);

		return $arrReturn;

	}



}
