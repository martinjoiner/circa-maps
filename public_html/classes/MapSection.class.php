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


	function __construct( $id, $x, $y, $xMargin = 100, $yMargin = 100 ){

		$this->id = $id;

		parent::extractMapFromDB();

		$this->xMin = parent::limitXToBoundaries( $x - $xMargin );
		$this->xMax = parent::limitXToBoundaries( $x + $xMargin );
		$this->yMin = parent::limitYToBoundaries( $y - $yMargin );
		$this->yMax = parent::limitYToBoundaries( $y + $yMargin );

		$this->extractRoutesFromDB();

		$this->extractPropertiesFromDB();
		
	}




	/**
	 Extracts all the data for the routes on this map inside the xMin, xMax, yMin and yMax boundaries
	 the reason we do this is because collision detection across the entire set of objects will be impossible when there are thousands of points
	 By leveraging a database index we can only run collission detection against objects that contain points that are resonably close
	*/
	private function extractRoutesFromDB(){

		include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

		$qry = $db->prepare("	SELECT 		`route`.`id`, `route`.`width` as 'routeWidth', `point`.`x`, `point`.`y`
								FROM 		`point` AS p1 
								LEFT JOIN 	`route` ON `route`.`id` = p1.`route_id` 
								LEFT JOIN 	`point` ON `point`.`route_id` = `route`.`id`
								WHERE 		`route`.`map_id` = :mapID 
								AND p1.x > :xMin 
								AND p1.x < :xMax 
								AND p1.y > :yMin 
								AND p1.y < :yMax 
								ORDER BY 	`route`.`id`, `point`.`order` 
							");
		$qry->bindValue('mapID', $this->id, PDO::PARAM_INT);
		$qry->bindValue('xMin', $this->xMin, PDO::PARAM_INT);
		$qry->bindValue('xMax', $this->xMax, PDO::PARAM_INT);
		$qry->bindValue('yMin', $this->yMin, PDO::PARAM_INT);
		$qry->bindValue('yMax', $this->yMax, PDO::PARAM_INT);
		$qry->execute();
		$rslt = $qry->fetchAll(PDO::FETCH_ASSOC);
		$qry->closeCursor();

		parent::processDBResult( $rslt, 'ROUTE' );

	}




	/**
	 Extracts all the data for the properties on this map inside the xMin, xMax, yMin and yMax boundaries
	 the reason we do this is because collision detection across the entire set of objects will be impossible when there are thousands of items on the map
	 By leveraging a database index we can only run collission detection against objects that contain points that are resonably close
	*/
	private function extractPropertiesFromDB(){

		include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

		$qry = $db->prepare("	SELECT 		`property`.`id`, `point`.`x`, `point`.`y`
								FROM 		`point` AS p1 
								LEFT JOIN 	`property` ON `property`.`id` = p1.`property_id` 
								LEFT JOIN 	`point` ON `point`.`property_id` = `property`.`id`
								WHERE 		`property`.`map_id` = :mapID 
								AND p1.x > :xMin 
								AND p1.x < :xMax 
								AND p1.y > :yMin 
								AND p1.y < :yMax 
								ORDER BY 	`property`.`id`, `point`.`order` 
							");
		$qry->bindValue('mapID', $this->id, PDO::PARAM_INT);
		$qry->bindValue('xMin', $this->xMin, PDO::PARAM_INT);
		$qry->bindValue('xMax', $this->xMax, PDO::PARAM_INT);
		$qry->bindValue('yMin', $this->yMin, PDO::PARAM_INT);
		$qry->bindValue('yMax', $this->yMax, PDO::PARAM_INT);
		$qry->execute();
		$rslt = $qry->fetchAll(PDO::FETCH_ASSOC);
		$qry->closeCursor();

		parent::processDBResult( $rslt, 'PROPERTY' );

	}




	/**
	 Takes a co-ordinate and returns true if there is a property sitting on that point or a route intersecting
	*/
	public function isOccupied( $x, $y ){

		$objMath = new Math();

		$arrResult = array( 'cntProperties'=>sizeof($this->arrProperties), 'cntRoutes'=>sizeof($this->arrRoutes), 'isOccupied'=>false, 'message'=>'');
		
		// Check if point is inside a property 
		foreach( $this->arrProperties as $thisProperty ){

			$points_polygon = count($thisProperty->arrPoints);  // number vertices - zero-based array

			if( $objMath->isInPolygon($points_polygon, $thisProperty->arrVerticesX, $thisProperty->arrVerticesY, $x, $y) ){
				$arrResult['isOccupied'] = true;
				$arrResult['message'] = $x . ',' . $y . ' is inside property ID ' . $thisProperty->id;
			} else { 
				//echo '<p>' . $thisX . ':' . $thisY . ' is not in polygon (' . implode(',',$thisPath->arrVerticesX) . '),(' . implode(',',$thisPath->arrVerticesY) . ')' . $points_polygon . '</p>';
			}

		}

		// Check if point is on a route


		return $arrResult;

	}



	/**
	 Places a property on the map if it's points do not collide
	*/
	public function placeProperty( $x, $y){

		$startTime = microtime(true);

		$arrResult = array( 'arrPath'=>null, 'success'=>false );

		// Generate 4 points based on supplied x and y
		$arrPoints = array();
		$arrPoints[] = array( 'x'=>$x, 'y'=>$y ); 
		$arrPoints[] = array( 'x'=>$x + 10, 'y'=>$y ); 
		$arrPoints[] = array( 'x'=>$x + 10, 'y'=>$y + 10 ); 
		$arrPoints[] = array( 'x'=>$x, 'y'=>$y + 10 ); 

		// Check all 4 points do not collide
		$isCollisionFree = true;
		foreach( $arrPoints as $thisPoint ){
			$arrIsOccupiedResult = $this->isOccupied( $thisPoint['x'], $thisPoint['y'] ); 
			if( $arrIsOccupiedResult['isOccupied'] ){
				$isCollisionFree = false;
			}
		}

		if( $isCollisionFree ){
			// Call the saveInDB method of the newly created Property 
			include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

			$qry = $db->prepare("	INSERT INTO `property` ( `map_id`, `name` )
									VALUES ( :mapID, 'new Property' );
								");
			$qry->bindValue('mapID', $this->id, PDO::PARAM_INT);
			$qry->execute();
			$propertyID = $db->lastInsertId();

			$qry = $db->prepare("	INSERT INTO `point` ( `property_id`, `order`, `x`, `y` )
									VALUES 	( :propertyID, 1, :x1, :y1 ),
											( :propertyID, 2, :x2, :y2 ),
											( :propertyID, 3, :x3, :y3 ),
											( :propertyID, 4, :x4, :y4 );
								");
			$qry->bindValue('propertyID', $propertyID, PDO::PARAM_INT);
			$qry->bindValue('x1', $arrPoints[0]['x'], PDO::PARAM_INT);
			$qry->bindValue('y1', $arrPoints[0]['y'], PDO::PARAM_INT);
			$qry->bindValue('x2', $arrPoints[1]['x'], PDO::PARAM_INT);
			$qry->bindValue('y2', $arrPoints[1]['y'], PDO::PARAM_INT);
			$qry->bindValue('x3', $arrPoints[2]['x'], PDO::PARAM_INT);
			$qry->bindValue('y3', $arrPoints[2]['y'], PDO::PARAM_INT);
			$qry->bindValue('x4', $arrPoints[3]['x'], PDO::PARAM_INT);
			$qry->bindValue('y4', $arrPoints[3]['y'], PDO::PARAM_INT);
			$qry->execute();
			$qry->closeCursor();
			

			// If no collisions init a Property object
			$this->arrProperties[] = new Property( $propertyID, $arrPoints );

			$arrResult['success'] = true;
			$arrResult['arrPath'] = $this->arrProperties[ sizeof($this->arrProperties)-1 ]->getPath();
		}

		$arrResult['executionTime'] = microtime(true) - $startTime;

		// Return result and arrPath
		return $arrResult;
	}


}

