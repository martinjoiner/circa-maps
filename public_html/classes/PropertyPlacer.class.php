<?php

/**
 This class is a map but with only the routes and properties in proximity to given coordinates
*/
class PropertyPlacer extends MapSection{


	/**
	 Places a property on the map if it's points do not collide
	*/
	public function placeProperty( $x, $y ){

		$startTime = microtime(true);

		$arrResult = array( 'arrPath'=>null, 'success'=>false );

		$objMath = new Math();

		// Get the nearest route
		$arrNearestRouteResult = $this->nearestRoute( $x, $y );
		$closestPointOnRoute = $arrNearestRouteResult['closestPointOnRoute'];


		if( is_null($closestPointOnRoute['distanceToPointResult']) || $closestPointOnRoute['distanceToPointResult'] > 50 ){
		 	return $arrResult;
		}

		// Generate 4 points based on supplied x and y
		$arrRearLeftPoint = array( 'x'=>$x, 'y'=>$y ); 
		$arrFrontLeftPoint = $objMath->pointPercentageBetweenPoints( $closestPointOnRoute['arrPointResult'], $arrRearLeftPoint, 20); 
		$arrFrontRightPoint = $objMath->ninetyDeg( $arrRearLeftPoint, $arrFrontLeftPoint ); 
		$arrRearRightPoint = $objMath->ninetyDeg( $arrFrontLeftPoint, $arrFrontRightPoint ); 
		$arrPoints = array( $arrFrontLeftPoint, $arrFrontRightPoint, $arrRearRightPoint, $arrRearLeftPoint );

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
