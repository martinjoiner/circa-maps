<?php

/**
 This is the very beginning of a settlement. It creates 2 routes that intersect. From there civilisation can begin
*/
class MapInitCrossRoads extends Map{


	function __construct( $id ){

		$this->id = $id;

		parent::extractMapFromDB();
		
	}




	/**
	 Places a property on the map if it's points do not collide
	*/
	public function generateCrossRoads(){

		$startTime = microtime(true);

		// Select a random point on the east edge of the map
		$startY = rand(0,$this->height);
		$startPoint = array( 'x'=>0, 'y'=>intval($startY) );

		// Select the inverted equivilent on the west side the map
		$endY = intval($this->height - $startPoint['y']);
		$endPoint = array( 'x'=>$this->width, 'y'=>$endY );

		$arrPoints = $this->generateRoute( $startPoint, $endPoint );

		$this->arrRoutes[] = new Route( 99, $arrPoints );


		// Repeat for the north and south edge
		$startX = rand(0, $this->width);
		$startPoint = array( 'x'=>intval($startX), 'y'=>0 );

		$endX = intval($this->width - $startPoint['x']);
		$endPoint = array( 'x'=>$endX, 'y'=>$this->height );

		$arrPoints = $this->generateRoute( $startPoint, $endPoint );

		// TO DO: $routeID = saveRouteInDB( $arrPoints );
		$this->arrRoutes[] = new Route( 101, $arrPoints );



		

		$arrResult['success'] = true;
		$arrResult['arrPaths'] = array();
		$arrResult['arrPaths'][0] = $this->arrRoutes[0]->getPath();
		$arrResult['arrPaths'][1] = $this->arrRoutes[1]->getPath();
		

		$arrResult['executionTime'] = microtime(true) - $startTime;

		// Return result and arrPath
		return $arrResult;
	}



	/**
	 Returns an array of points between a start and end point
	*/
	function generateRoute( $startPoint, $endPoint ){

		$objMath = new Math();

		$arrPoints = array();
		$arrPoints[] = $startPoint;

		$routeSectionLength = 50;

		// Walk a varying path between start and end points
		$pointer = 0;
		$distanceLeft = $objMath->distanceBetween( $arrPoints[$pointer], $endPoint );
		while( $distanceLeft > $routeSectionLength ){

			$percentageStep = round( $routeSectionLength / $distanceLeft * 100, 2 );

			$pointer++;

			$perfectPoint = $objMath->pointPercentageBetweenPoints( $arrPoints[$pointer-1], $endPoint, $percentageStep );

			$arrPoints[$pointer] = $objMath->randomVaryPoint($perfectPoint);

			// Set distance between last point and endPoint
			$distanceLeft = $objMath->distanceBetween( $arrPoints[$pointer], $endPoint );

		}

		return $arrPoints;
	}




	// DO TO: Save paths in database
	function saveRouteInDB( $arrPoints ){
		// include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

		// $qry = $db->prepare("	INSERT INTO `route` ( `map_id` )
		// 						VALUES ( :mapID );
		// 					");
		// $qry->bindValue('mapID', $this->id, PDO::PARAM_INT);
		// $qry->execute();
		// $routeID = $db->lastInsertId();

		$cnt = 1;
		foreach( $arrPoints as $thisPoint ){
			// $qry = $db->prepare("	INSERT INTO `point` ( `route_id`, `order`, `x`, `y` )
			// 						VALUES 	( :routeID, :order, :x, :y );
			// 					");
			// $qry->bindValue('routeID', $routeID, PDO::PARAM_INT);
			// $qry->bindValue('order', $cnt++, PDO::PARAM_INT);
			// $qry->bindValue('x', $thisPoint['x'], PDO::PARAM_INT);
			// $qry->bindValue('y', $thisPoint['y'], PDO::PARAM_INT);
			// $qry->execute();
		}
		// $qry->closeCursor();
	}


}

