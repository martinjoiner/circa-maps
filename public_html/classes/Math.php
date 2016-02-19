<?php

class Math{



	/**
	 * Returns a new point that is a certain distance between 2 points 
	 *
	 * @param {Point} $pointA 
	 * @param {Point} $pointB 
	 * @param {integer} $distance How far from pointA toward pointB should the result be
	 * 
	 * @return {Point} A point between 2 points 
	 */
	public static function pointDistanceBetweenPoints( Point $pointA, Point $pointB, $distance ){

		$resultPoint = new Point(); 

		// If the Xs are equal the line is vertical so just add or subtract distance depending on orientation
		if( $pointA->x == $pointB->x ){
			$resultPoint->x = $pointA->x;
			if( $pointB->y > $pointA->y ){
				$resultPoint->y = $pointA->y + $distance;
			} else {
				$resultPoint->y = $pointA->y - $distance;
			}
			return $resultPoint;
		}

		$oppositeSideToAngle = $pointA->y - $pointB->y;
		$adjascentSideToAngle = $pointB->x - $pointA->x;

		$tan = $oppositeSideToAngle / $adjascentSideToAngle;

		$angle = rad2deg( atan($tan) );

		$xDiff = cos( deg2rad($angle) ) * $distance;
		$yDiff = sin( deg2rad($angle) ) * $distance;


		if( $pointA->y > $pointB->y ){
			if( $pointA->x > $pointB->x ){
				// Above and left (working!)
				$newX = $pointA->x - $xDiff;
				$newY = $pointA->y + $yDiff;
			} else {

				$newX = $pointA->x + $xDiff;
				$newY = $pointA->y - $yDiff;
			}
		} else {
			if( $pointA->x > $pointB->x ){
				// Below and left (working!)
				$newX = $pointA->x - $xDiff;
				$newY = $pointA->y + $yDiff;
			} else {
				// Below and right (working!)
				$newX = $pointA->x + $xDiff;
				$newY = $pointA->y - $yDiff;
			}
		}
		$resultPoint->x = round( $newX, 2 );
		$resultPoint->y = round( $newY, 2 );

		return $resultPoint;
	}




	/**
	 * Returns the point that is some percentage along the path between arrPointA and arrPointB 
	 *
	 * @param {Point} $pointA 
	 * @param {Point} $pointB 
	 * @param {integer} $percent How far along the route from pointA the result should be
	 *
	 * @return {Point}
	 */
	public function pointPercentageBetweenPoints( Point $pointA, Point $pointB, $percent ){

		$percAsDec = 100 / $percent;

		$avX = $pointA->x + ( ($pointB->x - $pointA->x) / $percAsDec );
		$avY = $pointA->y + ( ($pointB->y - $pointA->y) / $percAsDec );

		return new Point( $avX, $avY );
	} 




	/**
	 * Returns the midway point between 2 given points
	 *
	 * @param {Point} $pointA 
	 * @param {Point} $pointB 
	 *
	 * @return {Point}
	 */
	public static function midPoint( Point $pointA, Point $pointB ){
		return static::pointPercentageBetweenPoints( $pointA, $pointB, 50 );
	}




	/**
	 * Returns weather a point is inside a polygon
	 *
	 * @param {integer} $cntPolygonPoints
	 * @param {array} $arrVerticesX
	 * @param {array} $arrVerticesY
	 * @param {Point} 
	 *
	 * @return {boolean} 0 or 1
	 */
	public static function isInPolygon($cntPolygonPoints, $arrVerticesX, $arrVerticesY, Point $point){

		$i = $j = $c = $pointer = 0;

		for( $i = 0, $j = $cntPolygonPoints-1; $i < $cntPolygonPoints; $j = $i++ ){
			$pointer = $i;

			// If i is past the final point, use the first point
			if( $pointer == $cntPolygonPoints ){
				$pointer = 0;
			}

			if ( ( ($arrVerticesY[$pointer] > $point->y != ($arrVerticesY[$j] > $point->y) ) &&
				($point->x < ($arrVerticesX[$j] - $arrVerticesX[$pointer]) * ($point->y - $arrVerticesY[$pointer]) / ($arrVerticesY[$j] - $arrVerticesY[$pointer]) + $arrVerticesX[$pointer]) ) ){
				$c = !$c;
			}
		}

		return $c;
	}




	/**
	 * Return the distance between 2 points
	 *
	 * @param {Point} $pointA 
	 * @param {Point} $pointB 
	 *
	 * @return {float} Distance between points
	 */
	public static function distanceBetween( Point $pointA, Point $pointB ){

		$x1 = floatval($pointA->x);
		$y1 = floatval($pointA->y);

		$x2 = floatval($pointB->x);
		$y2 = floatval($pointB->y);

		$xs = $x2 - $x1;
		$xs = $xs * $xs;

		$ys = $y2 - $y1;
		$ys = $ys * $ys;

		return sqrt( $xs + $ys );
	}




	/**
	 * Returns a Point projected a distace from pointA past pointB 
	 * (TODO: Needs testing, might operate the opposite of described above)
	 *
	 * @param {Point} $point1 associative array - The origin of the line of projection							
	 * @param {Point} $point2 associative array - The direction of the line of projection		
	 * @param percent The percentage the line must be extended by path second point 
	 *
	 * @return {Point}	
	 */
	public function projectPath( Point $pointA, Point $pointB, $percent = 10 ){

		$x = $pointA->x + ( ( $pointA->x - $pointB->x ) / $percent );
		$y = $pointA->y + ( ( $pointA->y - $pointB->y ) / $percent );

		return new Path( round($x, 3), round($y, 3) );
	}




	/**
	 * Returns the adjascent point based on 2 points 	
	 *
	 * @param {Point} arrPointA 
	 * @param {Point} arrPointB 										
	 * @param {boolean} clockwide *Optional* - Defaults to true 	
	 *
	 * @return {Point}
	 */
	public static function ninetyDeg( Point $pointA, Point $pointB, $clockwise = true ){

		$pointResult = new Point();

		if( $clockwise ){
			$pointResult->x = $pointB->x + ( $pointB->y - $pointA->y );
			$pointResult->y = $pointB->y - ( $pointB->x - $pointA->x );
		} else {
			$pointResult->x = $pointB->x - ( $pointB->y - $pointA->y );
			$pointResult->y = $pointB->y + ( $pointB->x - $pointA->x );
		}

		return $pointResult;
	}




	/**
	 * Returns true or false depending whether the $pointOrigin is above or below line
	 * (assumes pointA is to the left of pointB)
	 *
	 * @param {Point} $pointOrigin
	 * @param {Point} $pointA
	 * @param {Point} $pointB
	 *
	 * @return {boolean}
	 */
	function isOriginAboveLine( Point $pointOrigin, Point $pointA, Point $pointB ){

		// Discover if the line is ascending or descending
		$abOrientation = static::abOrientation( $pointA, $pointB );

		if( $abOrientation === 'flat' ){

			if( $pointOrigin->y < $pointB->y ){
				return true;
			} else {
				return false;
			}

		} else if( $abOrientation === 'descending' ){

			// If origin is left of A or below B in a descending line it is definitely below, return false.
			if( $pointOrigin->x <= $pointA->x || $pointOrigin->y >= $pointB->y ){
				return false;
			}

			// If origin is right of B or above A in a descending line it is definitely above, return true.
			if( $pointOrigin->x >= $pointB->x || $pointOrigin->y < $pointA->y ){
				return true;
			}

			// If we've reached this point of the function, origin must be inside the bounding box so we need to compare angles
			$oppositeSideToBLength = $pointOrigin->x - $pointA->x;

			$hypotenuseToBLength = static::distanceBetween( $pointOrigin, $pointA );

			$sinB = $oppositeSideToBLength / $hypotenuseToBLength;
			$angleB = rad2deg( asin( $sinB ) );

		} else if( $abOrientation === 'ascending' ) {

			// If origin is right of B or below A in an ascending line it is definitely below, return false.
			if( $pointOrigin->x > $pointB->x || $pointOrigin->y > $pointA->y ){
				return false;
			}

			// If origin is left of A or above B in an ascending line it is definitely above, return true.
			if( $pointOrigin->x < $pointA->x || $pointOrigin->y < $pointB->y ){
				return true;
			}

			// If we've reached this point of the function, origin must be inside the bounding box so we need to compare angles
			$oppositeSideToBLength = $pointB->x - $pointOrigin->x;
			$hypotenuseToBLength = static::distanceBetween( $pointB, $pointOrigin );
			if( $hypotenuseToBLength == 0 ){
				$sinB = 0;
			} else {
				$sinB = $oppositeSideToBLength / $hypotenuseToBLength;
			}
			$angleB = rad2deg( asin( $sinB ) );

		}

		$oppositeSideToALength = $pointB->x - $pointA->x;
		$hypotenuseToALength = static::distanceBetween( $pointB, $pointA );
		$sinA = $oppositeSideToALength / $hypotenuseToALength;
		$angleA = rad2deg( asin( $sinA ) );

		if( $angleA > $angleB ){
			return false;
		} else {
			return true;
		}

	}




	/**
	 * There are only 2 orientations between A and B, ascending (B is higher) or descending (B is lower)
	 * (assumes pointA is to the left of pointB)
	 *
	 * @param {Point}
	 * @param {Point}
	 *
	 * @return {string}
	 */
	public static function abOrientation( Point $pointA, Point $pointB ){
		if( $pointA->y === $pointB->y ){
			return 'flat';
		} else if( $pointA->y < $pointB->y ){
			return 'descending';
		} else {
			return 'ascending';
		}
	}



	/** 
	 * Swaps 2 points if required to ensure the left most point is point A
	 *
	 * @param {Point}
	 * @param {Point}
	 */
	public static function orientSoLeftmostIsFirst( Point &$pointA, Point &$pointB ){
		if( $pointA->x > $pointB->x ){
			$tempB = $pointB;
			$pointB = $pointA;
			$pointA = $tempB;
		}
	}




	/**
	 * Imagine you are stood looking at the side of a straight road that travels across your field of vision
	 * You know the coordinates of where you are ($pointOrigin), where the straight road starts and where it ends ($pointA and $pointB). 
	 * What is the coordinate of the point on that straight road that is directly infront of you, aka closest to you. 
	 *
	 * @param {Point} $pointOrigin 
	 * @param {Point} $pointA 
	 * @param {Point} $pointB 
	 *
	 * @return {array}
	 */
	public static function closestPointBetween2( Point $pointOrigin, Point $pointA, Point $pointB ){

		// Orient the points so that A is on the left
		static::orientSoLeftmostIsFirst( $pointA, $pointB );

		$abOrientation = static::abOrientation( $pointA, $pointB );
		$isOriginAboveLine = static::isOriginAboveLine( $pointOrigin, $pointA, $pointB );

		$arrReturn = array();

		// Angle a (Orange in docs) Calculate the angle of the corner nearest to pointA of a right-angled triangle with line between pointA and pointB as it's hypotenuse
		if( ( $abOrientation ==  'ascending' &&  $isOriginAboveLine ) || 
			( $abOrientation == 'descending' && !$isOriginAboveLine ) ){
			$arrRightAngleCornerPointToA = new Point($pointB->x, $pointA->y);
		} else { 
			$arrRightAngleCornerPointToA = new Point($pointA->x, $pointB->y);
		}
		$arrReturn['arrOppAndAdjSidesToA'] = [ 	$pointB,
												$arrRightAngleCornerPointToA, 
												$pointA
											];
		$oppositeSideToALength = static::distanceBetween( $pointB, $arrRightAngleCornerPointToA );
		$hypotenuseToALength = static::distanceBetween( $pointA, $pointB );
		if( $hypotenuseToALength == 0 ){
			$sinA = 0;
		} else {
			$sinA = $oppositeSideToALength / $hypotenuseToALength;
		}
		$angleA = rad2deg( asin( $sinA ) );

		// Calculate the angle of the corner nearest to pointA of a right-angled triangle with line between pointA and pointOrigin as it's hypotenuse
		if( ( $abOrientation ==  'ascending' &&  $isOriginAboveLine && $pointOrigin->x > $pointA->x ) || 
			( $abOrientation ==  'ascending' && !$isOriginAboveLine && $pointOrigin->y > $pointA->y ) || 
			( $abOrientation == 'descending' &&  $isOriginAboveLine && $pointOrigin->y < $pointA->y ) || 
			( $abOrientation == 'descending' && !$isOriginAboveLine && $pointOrigin->x > $pointA->x ) ){
			$arrRightAngleCornerPointToC = new Point($pointA->x, $pointOrigin->y);
		} else {
			$arrRightAngleCornerPointToC = new Point($pointOrigin->x, $pointA->y);
		}
		$arrReturn['arrOppAndAdjSidesToC'] = array( 	$pointOrigin,
														$arrRightAngleCornerPointToC, 
														$pointA
												);
		$oppositeSideToCLength = static::distanceBetween( $pointOrigin, $arrRightAngleCornerPointToC );
		$hypotenuseToCLength = static::distanceBetween( $pointOrigin, $pointA );
		if( $hypotenuseToCLength == 0 ){
			$sinC = 0;
		} else {
			$sinC = $oppositeSideToCLength / $hypotenuseToCLength;
		}
		$angleC = rad2deg( asin( $sinC ) );

		if( ( $abOrientation ==  'ascending' && $pointOrigin->x > $pointA->x && $pointOrigin->y <= $pointA->y ) ||
			( $abOrientation == 'descending' && $pointOrigin->x > $pointA->x && $pointOrigin->y >= $pointA->y ) ){
			$totalAngle = 90;
		} else {
			$totalAngle = 180;
		}

		// Calculate the angle where the line between pointA and pointB meets the line between pointA and pointOrigin
		$angleB = $totalAngle - $angleA - $angleC;

		// Now we have angleB and distance between pointA and pointOrigin we can calculate the distance of the line between pointOrigin and pointResult 
		$hypotenuseToBLength = $hypotenuseToCLength;
		$adjacentSideToB = cos( deg2rad($angleB) ) * $hypotenuseToBLength;

		$hypotenuseToDLength = $adjacentSideToB;
		$side1Length = cos( deg2rad($angleA) ) * $hypotenuseToDLength;
		$side2Length = sin( deg2rad($angleA) ) * $hypotenuseToDLength;

		if( $abOrientation == 'ascending' ){
			if( $isOriginAboveLine ){
				$resultX = $pointA->x + $side1Length;
				$resultY = $pointA->y - $side2Length;
			} else {
				$resultX = $pointA->x + $side2Length;
				$resultY = $pointA->y - $side1Length;
			}
		} else if( $abOrientation == 'descending' ) {
			if( $isOriginAboveLine ){
				$resultX = $pointA->x + $side2Length;
				$resultY = $pointA->y + $side1Length;
			} else {
				$resultX = $pointA->x + $side1Length;
				$resultY = $pointA->y + $side2Length;
			}
		}

		$arrPointResult = new Point( $resultX, $resultY );

		// Return some debugging
		$arrReturn['arrPointResult'] = $arrPointResult;
		$arrReturn['distanceToPointResult'] = static::distanceBetween( $pointOrigin, $arrPointResult );

		$arrReturn['arrPointA'] = $pointA;
		$arrReturn['arrPointB'] = $pointB;
		$arrReturn['abOrientation'] = $abOrientation;
		$arrReturn['isOriginAboveLine'] = $isOriginAboveLine;
		$arrReturn['oppositeSideToALength'] = $oppositeSideToALength;
		$arrReturn['hypotenuseToALength'] = $hypotenuseToALength;
		$arrReturn['sinA'] = $sinA;
		$arrReturn['angleA'] = $angleA;

		$arrReturn['oppositeSideToCLength'] = $oppositeSideToCLength;
		$arrReturn['hypotenuseToCLength'] = $hypotenuseToCLength;
		$arrReturn['sinC'] = $sinC;
		$arrReturn['angleC'] = $angleC;

		$arrReturn['angleB'] = $angleB;

		$arrReturn['hypotenuseToBLength'] = $hypotenuseToBLength;

		return $arrReturn;

	}




	/**
	 * Calculates area of a triangle. 
	 * for debugging purposes also returns the rightAngledTriangles used to calculate.
	 *
	 * @param {Point} $pointA
	 * @param {Point} $pointB
	 * @param {Point} $pointC
	 *
	 * @return {array} Contains: 'area' and 'rightAngledTriangles'
	 */
	public static function areaOfTriangle( Point $pointA, Point $pointB, Point $pointC ){

		$arrReturn['area'] = 0;
		$arrReturn['rightAngledTriangles'] = [ null, null ];

		$arrClosestPointBetween2 = static::closestPointBetween2( $pointA, $pointB, $pointC );

		$arrReturn['rightAngledTriangles'][0] = array( $pointA, $arrClosestPointBetween2['arrPointResult'], $pointB );
		$lengthOfBase = static::distanceBetween( $arrClosestPointBetween2['arrPointResult'], $pointB );
		$arrReturn['area'] += $lengthOfBase * $arrClosestPointBetween2['distanceToPointResult'] / 2;

		$arrReturn['rightAngledTriangles'][1] = array( $pointA, $arrClosestPointBetween2['arrPointResult'], $pointC );
		$lengthOfBase = static::distanceBetween( $arrClosestPointBetween2['arrPointResult'], $pointC );
		$arrReturn['area'] += $lengthOfBase * $arrClosestPointBetween2['distanceToPointResult'] / 2;

		$arrReturn['area'] = round( $arrReturn['area'], 2 );

		return $arrReturn;

	}




	/** 
	 * Takes a single point and searches an array of points to return the top n nearest
	 */
	public function nearestPointsInArray( $pointOrigin, $arrPoints, $resultLimit = 5, $distanceLimit = 100 ){
		
		$arrPointsWithinLimit = array();

		// Loop over $arrPoints calculating distance and assigning them to $arrPointsWithinLimit if within limit
		$iLimit = sizeof($arrPoints);
		for( $i = 0; $i < $iLimit; $i++ ){
			$arrPoints[$i]['distance'] = static::distanceBetween( $pointOrigin, $arrPoints[$i] );
			if( $arrPoints[$i]['distance'] < $distanceLimit ){
				$arrPointsWithinLimit[] = $arrPoints[$i];
			}
		}

		// Sort arrPointsWithinLimit by distance
		usort( $arrPointsWithinLimit, array( "Math", "comparePointsDistance" ) );

		return array_slice($arrPointsWithinLimit, 0, $resultLimit);
	}




	/**
	 Comparison function used by nearestPointsInArray() when sorting array
	*/
	private static function comparePointsDistance( $arrPoint1, $arrPoint2 ){
		if( $arrPoint1['distance'] > $arrPoint2['distance'] ){
			return 1;
		}
		return 0;
	}

	

}
