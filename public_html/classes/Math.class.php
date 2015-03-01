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


}
