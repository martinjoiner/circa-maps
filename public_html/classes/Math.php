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
	public function pointDistanceBetweenPoints( Point $pointA, Point $pointB, $distance ){

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
	 * Returns a midway point between 2 points 
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
	 Returns the a point that is some percentage along the path between arrPointA and arrPointB
	*/
	function midPoint( $pointA, $pointB ){
		return $this->pointPercentageBetweenPoints( $pointA, $pointB, 50 );
	}




	/**
	 * Returns the a point that is midway between arrPointA and arrPointB
	 */
	function isInPolygon($cntPolygonPoints, $arrVerticesX, $arrVerticesY, $x, $y){

		$i = $j = $c = $point = 0;

		for( $i = 0, $j = $cntPolygonPoints-1; $i < $cntPolygonPoints; $j = $i++ ){
			$point = $i;
			// If i is past the final point, use the first point
			if( $point == $cntPolygonPoints ){
				$point = 0;
			}
			if ( ( ($arrVerticesY[$point] > $y != ($arrVerticesY[$j] > $y) ) &&
				($x < ($arrVerticesX[$j] - $arrVerticesX[$point]) * ($y - $arrVerticesY[$point]) / ($arrVerticesY[$j] - $arrVerticesY[$point]) + $arrVerticesX[$point]) ) ){
				$c = !$c;
			}
		}
		return $c;

	}




	/**
	 * Return the distance between 2 points#
	 *
	 * @param {Point} $pointA 
	 * @param {Point} $pointB 
	 *
	 * @return {float} Distance between points
	 */
	function distanceBetween( Point $pointA, Point $pointB ){

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
	 Returns the co-ordinates of a point projected a distace from 2 points 
	 @arrPoint1 associative array - The origin of the line of projection							
	 @arrPoint2 associative array - The direction of the line of projection		
	 @percent The percentage the line must be extended by path second point 	
	*/
	function projectPath( $arrPoint1, $arrPoint2, $percent = 10 ){

		$x1 = $arrPoint1['x'];
		$y1 = $arrPoint1['y'];

		$x2 = $arrPoint2['x'];
		$y2 = $arrPoint2['y'];

		$x3 = $x1 + ( ( $x1 - $x2 ) / $percent );
		$y3 = $y1 + ( ( $y1 - $y2 ) / $percent );

		return round($x3, 3) + ',' + round($y3, 3);
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
	function ninetyDeg( Point $pointA, Point $pointB, $clockwise = true ){

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
		$abOrientation = $this->abOrientation( $pointA, $pointB );

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

			$hypotenuseToBLength = $this->distanceBetween( $pointOrigin, $pointA );

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
			$hypotenuseToBLength = $this->distanceBetween( $pointB, $pointOrigin );
			if( $hypotenuseToBLength == 0 ){
				$sinB = 0;
			} else {
				$sinB = $oppositeSideToBLength / $hypotenuseToBLength;
			}
			$angleB = rad2deg( asin( $sinB ) );

		}

		$oppositeSideToALength = $pointB->x - $pointA->x;
		$hypotenuseToALength = $this->distanceBetween( $pointB, $pointA );
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
	function abOrientation( Point $pointA, Point $pointB ){
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
	function orientSoLeftmostIsFirst( Point &$pointA, Point &$pointB ){
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
	function closestPointBetween2( Point $pointOrigin, Point $pointA, Point $pointB ){

		// Orient the points so that A is on the left
		$this->orientSoLeftmostIsFirst( $pointA, $pointB );

		$abOrientation = $this->abOrientation( $pointA, $pointB );
		$isOriginAboveLine = $this->isOriginAboveLine( $pointOrigin, $pointA, $pointB );

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
		$oppositeSideToALength = $this->distanceBetween( $pointB, $arrRightAngleCornerPointToA );
		$hypotenuseToALength = $this->distanceBetween( $pointA, $pointB );
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
		$oppositeSideToCLength = $this->distanceBetween( $pointOrigin, $arrRightAngleCornerPointToC );
		$hypotenuseToCLength = $this->distanceBetween( $pointOrigin, $pointA );
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
		$arrReturn['distanceToPointResult'] = $this->distanceBetween( $pointOrigin, $arrPointResult );

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
	public function areaOfTriangle( Point $pointA, Point $pointB, Point $pointC ){

		$arrReturn['area'] = 0;
		$arrReturn['rightAngledTriangles'] = [ null, null ];

		$arrClosestPointBetween2 = $this->closestPointBetween2( $pointA, $pointB, $pointC );

		$arrReturn['rightAngledTriangles'][0] = array( $pointA, $arrClosestPointBetween2['arrPointResult'], $pointB );
		$lengthOfBase = $this->distanceBetween( $arrClosestPointBetween2['arrPointResult'], $pointB );
		$arrReturn['area'] += $lengthOfBase * $arrClosestPointBetween2['distanceToPointResult'] / 2;

		$arrReturn['rightAngledTriangles'][1] = array( $pointA, $arrClosestPointBetween2['arrPointResult'], $pointC );
		$lengthOfBase = $this->distanceBetween( $arrClosestPointBetween2['arrPointResult'], $pointC );
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
			$arrPoints[$i]['distance'] = $this->distanceBetween( $pointOrigin, $arrPoints[$i] );
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
