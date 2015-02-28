<?php

class Math{

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

	function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y){

		$i = $j = $c = 0;
		for ($i = 0, $j = $points_polygon ; $i < $points_polygon; $j = $i++) {
		if ( (($vertices_y[$i]  >  $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
			($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]) ) )
			$c = !$c;
		}
		return $c;

	}


}
