<?php

class Math{



	/**
	 Returns the a point between 2 points 
	 $arrPointA, $arrPointB associative array with 'x' and 'y' values
	 $distance How far from pointA toward pointB should the result be
	*/
	function pointDistanceBetweenPoints( $arrPointA, $arrPointB, $distance ){
		$oppositeSideToAngle = $arrPointA['y'] - $arrPointB['y'];
		$adjascentSideToAngle = $arrPointB['x'] - $arrPointA['x'];
		$tan = $oppositeSideToAngle / $adjascentSideToAngle;

		$angle = rad2deg( atan($tan) );

		$xDiff = cos( deg2rad($angle) ) * $distance;
		$yDiff = sin( deg2rad($angle) ) * $distance;

		if( $arrPointA['y'] > $arrPointB['y'] ){
			if( $arrPointA['x'] > $arrPointB['x'] ){
				// Above and left (working!)
				 $newX = $arrPointA['x'] - $xDiff;
				 $newY = $arrPointA['y'] + $yDiff;
			} else {

				$newX = $arrPointA['x'] + $xDiff;
				$newY = $arrPointA['y'] - $yDiff;
			}
		} else {
			if( $arrPointA['x'] > $arrPointB['x'] ){
				// Below and left (working!)
				$newX = $arrPointA['x'] - $xDiff;
				$newY = $arrPointA['y'] + $yDiff;
			} else {
				// Below and right (working!)
				$newX = $arrPointA['x'] + $xDiff;
				$newY = $arrPointA['y'] - $yDiff;
			}
		}
		$arrResultPoint = array( 'x'=>$newX, 'y'=>$newY );

		return $arrResultPoint;
	}




	/**
	 Returns a midway point between 2 points 
	 $arrPointA, $arrPointB associative array with 'x' and 'y' values
	 $percent How far along the route from pointA the result should be
	*/
	function pointPercentageBetweenPoints( $arrPointA, $arrPointB, $percent ){

		$x1 = $arrPointA['x']; 
		$y1 = $arrPointA['y']; 

		$x2 = $arrPointB['x']; 
		$y2 = $arrPointB['y']; 

		$percAsDec = 100 / $percent;

		$avX = $x1 + ( ($x2 - $x1) / $percAsDec );
		$avY = $y1 + ( ($y2 - $y1) / $percAsDec );

		$arrReturnPoint = array( 'x'=>$avX, 'y'=>$avY );

		return $arrReturnPoint;
	} 




	/**
	 Returns the a point that is some percentage along the path between arrPointA and arrPointB
	*/
	function midPoint( $arrPointA, $arrPointB ){
		return $this->pointPercentageBetweenPoints( $arrPointA, $arrPointB, 50 );
	}




	/**
	 Alters the location of a point by a random amount to fake organic positioning
	 $maxVary Maximum number of units by which the point can vary on the x or y axis
	*/
	function randomVaryPoint( $arrPoint, $maxVary = 10 ){
		
		$x = $arrPoint['x'];
		$y = $arrPoint['y'];
		
		$newX = $x + ( rand(0,$maxVary) - ($maxVary/2) );
		$newY = $y + ( rand(0,$maxVary) - ($maxVary/2) );
		
		$arrReturnPoint = array( 'x'=>$newX, 'y'=>$newY );
		return $arrReturnPoint;
	}




	/**
	 Returns the a point that is midway between arrPointA and arrPointB
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
	 Return the distance between 2 points
	 $arrPointA - Associative array with 2 elements with the keys 'x' and 'y'
	 $arrPointB - Same as above
	*/
	function distanceBetween( $arrPointA, $arrPointB ){

		$x1 = floatval($arrPointA['x']);
		$y1 = floatval($arrPointA['y']);

		$x2 = floatval($arrPointB['x']);
		$y2 = floatval($arrPointB['y']);

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
	 Returns the adjascent point based on 2 points 	
	 @arrPointA, @arrPointB - associative array describing a point											
	 @clockwide *Optional* - Boolean - Defaults to true 	
	*/
	function ninetyDeg( $arrPointA, $arrPointB, $clockwise = true ){

		$arrPointResult = array();

		if( $clockwise ){
			$arrPointResult['x'] = $arrPointB['x'] + ( $arrPointB['y'] - $arrPointA['y'] );
			$arrPointResult['y'] = $arrPointB['y'] - ( $arrPointB['x'] - $arrPointA['x'] );
		} else {
			$arrPointResult['x'] = $arrPointB['x'] - ( $arrPointB['y'] - $arrPointA['y'] );
			$arrPointResult['y'] = $arrPointB['y'] + ( $arrPointB['x'] - $arrPointA['x'] );
		}

		return $arrPointResult;
	}




	/**
	 Returns true or false depending whether the $arrPointOrigin is above or below line
	 (assumes pointA is to the left of pointB)
	 */
	function isOriginAboveLine( $arrPointOrigin, $arrPointA, $arrPointB ){
		// Discover if the line is ascending or descending
		$abOrientation = $this->abOrientation( $arrPointA, $arrPointB );

		if( $abOrientation === 'descending' ){

			// If origin is left of A or below B in a descending line it is definitely below, return false.
			if( $arrPointOrigin['x'] < $arrPointA['x'] || $arrPointOrigin['y'] > $arrPointB['y'] ){
				return false;
			}

			// If origin is right of B or above A in a descending line it is definitely above, return true.
			if( $arrPointOrigin['x'] > $arrPointB['x'] || $arrPointOrigin['y'] < $arrPointA['y'] ){
				return true;
			}

			// If we've reached this point of the function, origin must be inside the bounding box so we need to compare angles
			$oppositeSideToBLength = $arrPointOrigin['x'] - $arrPointA['x'];
			$hypotenuseToBLength = $this->distanceBetween( $arrPointOrigin, $arrPointA );
			$sinB = $oppositeSideToBLength / $hypotenuseToBLength;
			$angleB = rad2deg( asin( $sinB ) );

		} else if( $abOrientation === 'ascending' ) {

			// If origin is right of B or below A in an ascending line it is definitely below, return false.
			if( $arrPointOrigin['x'] > $arrPointB['x'] || $arrPointOrigin['y'] > $arrPointA['y'] ){
				return false;
			}

			// If origin is left of A or above B in an ascending line it is definitely above, return true.
			if( $arrPointOrigin['x'] < $arrPointA['x'] || $arrPointOrigin['y'] < $arrPointB['y'] ){
				return true;
			}

			// If we've reached this point of the function, origin must be inside the bounding box so we need to compare angles
			$oppositeSideToBLength = $arrPointB['x'] - $arrPointOrigin['x'];
			$hypotenuseToBLength = $this->distanceBetween( $arrPointB, $arrPointOrigin );
			$sinB = $oppositeSideToBLength / $hypotenuseToBLength;
			$angleB = rad2deg( asin( $sinB ) );

		}

		$oppositeSideToALength = $arrPointB['x'] - $arrPointA['x'];
		$hypotenuseToALength = $this->distanceBetween( $arrPointB, $arrPointA );
		$sinA = $oppositeSideToALength / $hypotenuseToALength;
		$angleA = rad2deg( asin( $sinA ) );

		if( $angleA > $angleB ){
			return false;
		} else {
			return true;
		}

	}




	/**
	 There are only 2 orientations between A and B, ascending (B is higher) or descending (B is lower)
	 (assumes pointA is to the left of pointB)
	*/
	function abOrientation( $arrPointA, $arrPointB ){
		if( $arrPointA['y'] < $arrPointB['y'] ){
			return 'descending';
		} else {
			return 'ascending';
		}
	}




	/**
	 Imagine you are stood looking at the side of a straight road that travels across your field of vision
	 You know the coordinates of where you are, where the straight road starts and where it ends 
	 What is the coordinate of the point on that straight road that is directly infront of you, aka closest to you. 
	 $arrOriginPoint - associative array with 'x' and 'y' values
	 $arrPointA - Same as above
	 $arrPointB - Same as above
	*/
	function closestPointBetween2( $arrPointOrigin, $arrPointA, $arrPointB ){

		// Orient the points so that A is on the left
		if( $arrPointA['x'] > $arrPointB['x'] ){
			$tempB = $arrPointB;
			$arrPointB = $arrPointA;
			$arrPointA = $tempB;
		}

		$abOrientation = $this->abOrientation( $arrPointA, $arrPointB );
		$isOriginAboveLine = $this->isOriginAboveLine( $arrPointOrigin, $arrPointA, $arrPointB );

		$arrReturn = array();

		// Angle a (Orange in docs) Calculate the angle of the corner nearest to pointA of a right-angled triangle with line between pointA and pointB as it's hypotenuse
		if( ( $abOrientation ==  'ascending' &&  $isOriginAboveLine ) || 
			( $abOrientation == 'descending' && !$isOriginAboveLine ) ){
			$arrRightAngleCornerPointToA = array('x'=>$arrPointB['x'], 'y'=>$arrPointA['y']);
		} else { 
			$arrRightAngleCornerPointToA = array('x'=>$arrPointA['x'], 'y'=>$arrPointB['y']);
		}
		$arrReturn['arrOppAndAdjSidesToA'] = array( 	$arrPointB,
														$arrRightAngleCornerPointToA, 
														$arrPointA
												);
		$oppositeSideToALength = $this->distanceBetween( $arrPointB, $arrRightAngleCornerPointToA );
		$hypotenuseToALength = $this->distanceBetween( $arrPointA, $arrPointB );
		$sinA = $oppositeSideToALength / $hypotenuseToALength;
		$angleA = rad2deg( asin( $sinA ) );

		// Calculate the angle of the corner nearest to pointA of a right-angled triangle with line between pointA and pointOrigin as it's hypotenuse
		if( ( $abOrientation ==  'ascending' &&  $isOriginAboveLine && $arrPointOrigin['x'] > $arrPointA['x'] ) || 
			( $abOrientation ==  'ascending' && !$isOriginAboveLine && $arrPointOrigin['y'] > $arrPointA['y'] ) || 
			( $abOrientation == 'descending' &&  $isOriginAboveLine && $arrPointOrigin['y'] < $arrPointA['y'] ) || 
			( $abOrientation == 'descending' && !$isOriginAboveLine && $arrPointOrigin['x'] > $arrPointA['x'] ) ){
			$arrRightAngleCornerPointToC = array('x'=>$arrPointA['x'], 'y'=>$arrPointOrigin['y']);
		} else {
			$arrRightAngleCornerPointToC = array('x'=>$arrPointOrigin['x'], 'y'=>$arrPointA['y']);
		}
		$arrReturn['arrOppAndAdjSidesToC'] = array( 	$arrPointOrigin,
														$arrRightAngleCornerPointToC, 
														$arrPointA
												);
		$oppositeSideToCLength = $this->distanceBetween( $arrPointOrigin, $arrRightAngleCornerPointToC );
		$hypotenuseToCLength = $this->distanceBetween( $arrPointOrigin, $arrPointA );
		$sinC = $oppositeSideToCLength / $hypotenuseToCLength;
		$angleC = rad2deg( asin( $sinC ) );

		if( ( $abOrientation ==  'ascending' && $arrPointOrigin['x'] > $arrPointA['x'] && $arrPointOrigin['y'] <= $arrPointA['y'] ) ||
			( $abOrientation == 'descending' && $arrPointOrigin['x'] > $arrPointA['x'] && $arrPointOrigin['y'] >= $arrPointA['y'] ) ){
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
				$resultX = $arrPointA['x'] + $side1Length;
				$resultY = $arrPointA['y'] - $side2Length;
			} else {
				$resultX = $arrPointA['x'] + $side2Length;
				$resultY = $arrPointA['y'] - $side1Length;
			}
		} else if( $abOrientation == 'descending' ) {
			if( $isOriginAboveLine ){
				$resultX = $arrPointA['x'] + $side2Length;
				$resultY = $arrPointA['y'] + $side1Length;
			} else {
				$resultX = $arrPointA['x'] + $side1Length;
				$resultY = $arrPointA['y'] + $side2Length;
			}
		}

		$arrPointResult = array( 'x'=>$resultX, 'y'=>$resultY );

		// Return some debugging
		$arrReturn['arrPointResult'] = $arrPointResult;
		$arrReturn['distanceToPointResult'] = $this->distanceBetween( $arrPointOrigin, $arrPointResult );

		$arrReturn['arrPointA'] = $arrPointA;
		$arrReturn['arrPointB'] = $arrPointB;
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
	 Returns an associative array containing area and for debugging purposes, the rightAngledTriangles used to calculate
	*/
	public function areaOfTriangle( $arrPointA, $arrPointB, $arrPointC ){

		$arrReturn['area'] = 0;
		$arrReturn['rightAngledTriangles'] = array();

		$arrClosestPointBetween2 = $this->closestPointBetween2( $arrPointA, $arrPointB, $arrPointC );

		$arrReturn['rightAngledTriangles'][0] = array( $arrPointA, $arrClosestPointBetween2['arrPointResult'], $arrPointB );
		$lengthOfBase = $this->distanceBetween( $arrClosestPointBetween2['arrPointResult'], $arrPointB );
		$arrReturn['area'] += $lengthOfBase * $arrClosestPointBetween2['distanceToPointResult'] / 2;

		$arrReturn['rightAngledTriangles'][1] = array( $arrPointA, $arrClosestPointBetween2['arrPointResult'], $arrPointC );
		$lengthOfBase = $this->distanceBetween( $arrClosestPointBetween2['arrPointResult'], $arrPointC );
		$arrReturn['area'] += $lengthOfBase * $arrClosestPointBetween2['distanceToPointResult'] / 2;

		$arrReturn['area'] = round( $arrReturn['area'], 2 );

		return $arrReturn;

	}

	

}
