<?php

class Svg{

	var $id = 0;
	var $arrPaths = array();
	var $width = 0;
	var $height = 0;
	var $name = 'New Map';

	function __construct( $id = null ){

		$this->id = $id;
		$this->extractPathsFromDB();
		$this->placeRandPath();
	}




	/**
	 Extracts all the paths for this map, loops through creating a Path object in the arrPaths array for each
	*/
	private function extractPathsFromDB(){
		$arrReturn = array();

		include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

		$qry = $db->prepare("	SELECT 		`map`.id AS mapID, `path`.id AS pathID, width, height, d, name
								FROM 		`map`
								LEFT JOIN 	`path` ON `path`.`map_id` = `map`.`id`
								WHERE `map`.`id` = :id 
							");
		$qry->bindValue('id', $this->id, PDO::PARAM_INT);
		$qry->execute();
		$rslt = $qry->fetchAll(PDO::FETCH_ASSOC);
		$qry->closeCursor();

		foreach( $rslt as $thisResult ){
			$this->id 			= $thisResult['mapID'];
			$this->width 		= $thisResult['width'];
			$this->height 		= $thisResult['height'];
			$this->name 		= $thisResult['name'];
			$this->arrPaths[] 	= new Path( $thisResult['pathID'], $thisResult['d'] );
		}

	}



	/**
	 IN DEV
	*/
	public function placeRandPath(){
		// Know the minimum area of footprint you want to build on
		$desArea = 80 * 80; // Desired area of property

		// Walk away from AAP, until in unoccupied space, set point1.
		// Try stepping 90 deg to AAP.
		// Try stepping closer to AAP.
		// Repeat previous 2 steps until both are failing, set point2.
		// Clone these 2 points and translate them away from their roots by half the distance between them
		// If point3 is in occupied space, move it closer toward point4 and record the distance required to get in free space.
		// Attempt to move point 4 that same distance away, monitoring area, when desired area is reached. Declare the property.

	}




	/**
	 Gets the Average Area of Points on the map. In other words, the population centre
	*/
	public function getAAP(){
		$returnResult = '';
		foreach( $this->arrPaths as $thisPath ){
			$returnResult = $thisPath->getCenter();
		}
		return $returnResult;
	}




	/**
	 Returns HTML markup of an svg. 
	 @includePaths boolean dictates whether the paths are included or just a blank template
	*/
	public function printMarkup( $includePaths = true ){
		$html = '<svg 	xmlns="http://www.w3.org/2000/svg"
					   	width="' . $this->width . '" 
					   	height="' . $this->height . '" 
					   	id="svg2">';
		if( $includePaths ){
			foreach( $this->arrPaths as $thisPath ){
				$html .= $thisPath->printMarkup();
			}
		}

		$html .= '</svg>';
		return $html;	

	}




	/**
	 takes a co-ordinate and returns true if there is a path sitting on that point
	*/
	public function isOccupied( $coord ){

		$coordParts = explode(',', trim($coord) );
		$thisX = $coordParts[0];
		$thisY = $coordParts[1];
		$objMath = new Math();
		
		foreach($this->arrPaths as $thisPath ){
		
			$points_polygon = count($thisPath->arrVerticesX);  // number vertices - zero-based array

			if ( $objMath->is_in_polygon($points_polygon, $thisPath->arrVerticesX, $thisPath->arrVerticesY, $thisX, $thisY) ){
				return true;
				//echo '<p>' . $thisX . ':' . $thisY . ' is in polygon! (' . implode(',',$thisPath->arrVerticesX) . '),(' . implode(',',$thisPath->arrVerticesY) . ')' . $points_polygon . '</p>';
			}
			else{ 
				//echo '<p>' . $thisX . ':' . $thisY . ' is not in polygon (' . implode(',',$thisPath->arrVerticesX) . '),(' . implode(',',$thisPath->arrVerticesY) . ')' . $points_polygon . '</p>';
			}

		}
		return false;

	}

}

?>