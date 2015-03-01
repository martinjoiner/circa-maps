<?php

class Math{


	/**
	 Returns the midway point between 2 points (ie.)
	 TODO: This needs converting to take arrPoints with x and y key-value pairs
	*/
	function midPoint( $pointA, $pointB ){

		$aParts = explode(',',$pointA);
		$bParts = explode(',',$pointB);

		$x1 = $aParts[0]; 
		$x2 = $bParts[0]; 

		$y1 = $aParts[1]; 
		$y2 = $bParts[1]; 

		$avX = ($x1 + $x2) / 2;
		$avY = ($y1 + $y2) / 2;
		return $avX . ',' . $avY;
	} 




	/**
	 
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

}
