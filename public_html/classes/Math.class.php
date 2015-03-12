<?php

class Math{


	/**
	 Returns the midway point between 2 points (ie.)
	 $arrPointA associative array with 'x' and 'y' values
	 $arrPointB Same as above
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
	 BUGGY NOT LIVE-READY
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

		// Now there are only 2 orientations between A and B, ascending (B is higher) or descending (B is lower)
		if( $arrPointA['y'] < $arrPointB['y'] ){
			$abOrientation = 'descending';
		} else {
			$abOrientation = 'ascending';
		}

		$arrReturn = array();

		// Calculate the angle of the corner nearest to pointA of a right-angled triangle with line between pointA and pointB as it's hypotenuse
		$arrRightAngleCornerPointToA = array('x'=>$arrPointA['x'], 'y'=>$arrPointB['y']);
		$arrReturn['arrOppAndAdjSidesToA'] = array( 	$arrPointB,
														$arrRightAngleCornerPointToA, 
														$arrPointA
												);
		$oppositeSideToALength = $this->distanceBetween( $arrPointB, $arrRightAngleCornerPointToA );
		$hypotenuseToALength = $this->distanceBetween( $arrPointA, $arrPointB );
		$sinA = $oppositeSideToALength / $hypotenuseToALength;
		$angleA = rad2deg( asin( $sinA ) );

		// Calculate the angle of the corner nearest to pointA of a right-angled triangle with line between pointA and pointOrigin as it's hypotenuse
		$arrRightAngleCornerPointToA = array('x'=>$arrPointA['x'], 'y'=>$arrPointOrigin['y']);
		$arrReturn['arrOppAndAdjSidesToC'] = array( 	$arrPointOrigin,
														$arrRightAngleCornerPointToA, 
														$arrPointA
												);
		$oppositeSideToCLength = $this->distanceBetween( $arrPointOrigin, $arrRightAngleCornerPointToA );
		$hypotenuseToCLength = $this->distanceBetween( $arrPointOrigin, $arrPointA );
		$sinC = $oppositeSideToCLength / $hypotenuseToCLength;
		$angleC = rad2deg( asin( $sinC ) );

		// Calculate the angle where the line between pointA and pointB meets the line between pointA and pointOrigin
		$angleB = 180 - $angleA - $angleC;

		// Now we have angleA and distance between pointA and pointResult we can calculate the coordinates
		$hypotenuseToBLength = $hypotenuseToCLength;
		$adjacentSideToB = cos( deg2rad($angleB) ) * $hypotenuseToBLength;

		$hypotenuse = $adjacentSideToB;
		$yDiff = cos( deg2rad($angleA) ) * $hypotenuse;
		$xDiff = sin( deg2rad($angleA) ) * $hypotenuse;

		
		$resultX = $arrPointA['x'] - $xDiff;
		$resultY = $arrPointA['y'] - $yDiff;


		$arrReturn['arrPointResult'] = array( 'x'=>$resultX, 'y'=>$resultY );

		// Return some debugging
		$arrReturn['arrPointA'] = $arrPointA;
		$arrReturn['arrPointB'] = $arrPointB;
		$arrReturn['abOrientation'] = $abOrientation;
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

}
