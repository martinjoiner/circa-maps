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
		$arrFrontLeftPoint = $objMath->pointDistanceBetweenPoints( $closestPointOnRoute['arrPointResult'], $arrRearLeftPoint, 8); 
		$arrFrontRightPoint = $objMath->ninetyDeg( $arrRearLeftPoint, $arrFrontLeftPoint ); 
		$arrRearRightPoint = $objMath->ninetyDeg( $arrFrontLeftPoint, $arrFrontRightPoint ); 

		$arrRearLeftPoint = $objMath->randomVaryPoint( $arrRearLeftPoint, 5 );
		$arrRearRightPoint = $objMath->randomVaryPoint( $arrRearRightPoint, 5 );

		$arrPoints = array( $arrFrontLeftPoint, $arrFrontRightPoint, $arrRearRightPoint, $arrRearLeftPoint );

		// Initialise an object to represent our proposed property 
		$objProposedProperty = new Property( NULL, $arrPoints );

		// Check for collision with all properties on map
		$isCollisionFree = true;
		foreach( $this->arrProperties as $objThisProperty ){
			if( $this->propertyCollision( $objProposedProperty, $objThisProperty ) ){
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




	/**
	 Tests 2 properties to see if they are overlapping 
	 http://content.gpwiki.org/index.php/Polygon_Collision 
	*/
	function propertyCollision( $objProperty1, $objProperty2 ){

		$objMath = new Math();

		$arrProperty1Data = $objProperty1->getCenterData();
		$arrProperty2Data = $objProperty2->getCenterData();

		$distanceBetweenCentres = $objMath->distanceBetween( $arrProperty1Data['arrCenterPoint'], $arrProperty2Data['arrCenterPoint'] );
		$sumOfFarthestRadii = $arrProperty1Data['farthestRadius'] + $arrProperty2Data['farthestRadius'];

		// Firstly: If distance between mid points is more than sum of maximum radius
		// there definitely cannot be a collision
		if( $distanceBetweenCentres > $sumOfFarthestRadii ){
			//console.warn('polyCollision(): Failed at stage 1');
			return false;
		}

		// Next, check if distance is less than either path's nearestRadius
		// If so there definitely *is* a collision! This is good for checking paths that are exactly on top of each other.
		if( $distanceBetweenCentres < $arrProperty1Data['nearestRadius'] || $distanceBetweenCentres < $arrProperty2Data['nearestRadius'] ){
			//console.warn('polyCollision(): Failed at stage 2');
			return true;
		}

		// Next check if any of $arrPoints1's points are inside $arrPoints2, If so: return true;
		foreach( $objProperty1->arrPoints as $arrThisPoint ){
			$points_polygon = count($objProperty2->arrPoints);  // number vertices - zero-based array
			if( $objMath->isInPolygon($points_polygon, $objProperty2->arrVerticesX, $objProperty2->arrVerticesY, $arrThisPoint['x'], $arrThisPoint['y'] ) ){
				return true;
			}
		}

		// Next check if any of $arrPoints1's points are inside $arrPoints2, If so: return true;
		foreach( $objProperty2->arrPoints as $arrThisPoint ){
			$points_polygon = count($objProperty1->arrPoints);  // number vertices - zero-based array
			if( $objMath->isInPolygon($points_polygon, $objProperty1->arrVerticesX, $objProperty1->arrVerticesY, $arrThisPoint['x'], $arrThisPoint['y'] ) ){
				return true;
			}
		}

		// TODO: Finally use the more expensive method of separating Axis which will account for very rare cases when mid sections overlap

		// If we've reached this far without detecting a collision, return false
		return false;
	}



	
}
