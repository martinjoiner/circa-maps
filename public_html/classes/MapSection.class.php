<?php

/**
 This class is a map but with only the routes and properties in proximity to given coordinates
*/
class MapSection extends Map{

	var $x;
	var $y;
	var $xMargin;
	var $yMargin;

	var $xMin;
	var $xMax;
	var $yMin;
	var $yMax;


	function __construct( $id, $x, $y, $xMargin, $yMargin ){

		$this->id = $id;

		$this->xMin = $this->limitXToBoundaries( $x - $xMargin );
		$this->xMax = $this->limitXToBoundaries( $x + $xMargin );
		$this->yMin = $this->limitYToBoundaries( $y - $yMargin );
		$this->yMax = $this->limitYToBoundaries( $y + $yMargin );

		parent::extractMapFromDB();

		$this->extractRoutesFromDB();

		//$this->extractPropertiesFromDB();
		
	}




	/**
	 Extracts all the data for the routes on this map inside the xMin, xMax, yMin and yMax boundaries
	 the reason we do this is because collision detection across the entire set of objects will be impossible when there are thousands of points
	 By leveraging a database index we can only run collission detection against objects that contain points that are resonably close
	*/
	private function extractRoutesFromDB(){

		include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

		$qry = $db->prepare("	SELECT 		`route`.`id` AS routeID, `point`.`x`, `point`.`y`
								FROM 		`point` AS p1 
								LEFT JOIN 	`route` AS r1 ON r1.`id` = `point`.`route_id` 
								LEFT JOIN 	`map` ON  `map`.`id` = `route`.`map_id`
								LEFT JOIN 	`route` ON `route`.`map_id` = `map`.`id`
								LEFT JOIN 	`point` ON `point`.`route_id` = `route`.`id`
								WHERE 		`map`.`id` = :mapID 
								AND p1.x > :xMin 
								AND p1.x < :xMax 
								AND p1.y > :yMin 
								AND p1.y < :yMax 
								ORDER BY 	`point`.`order` 
							");
		$qry->bindValue('mapID', $this->id, PDO::PARAM_INT);
		$qry->bindValue('xMin', $this->xMin, PDO::PARAM_INT);
		$qry->bindValue('xMax', $this->xMax, PDO::PARAM_INT);
		$qry->bindValue('yMin', $this->yMin, PDO::PARAM_INT);
		$qry->bindValue('yMax', $this->yMax, PDO::PARAM_INT);
		$qry->execute();
		$rslt = $qry->fetchAll(PDO::FETCH_ASSOC);
		$qry->closeCursor();

		parent::processRoutesFromDBResult( $rslt );

	}





}

