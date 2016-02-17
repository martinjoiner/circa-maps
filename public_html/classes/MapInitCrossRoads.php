<?php

/**
 * This is the very beginning of a settlement. It creates 2 routes that intersect. From there civilisation can begin
 */
class MapInitCrossRoads extends Map{




	/** 
	 * @constructor
	 *
	 * @param {integer} $id 
	 */
	public function __construct( $id ){

		$this->id = $id;

		parent::extractMapFromDB();
		
	}




	/** Do nothing here */
	protected function extractRoutesFromDB(){ }




	/**
	 * Draws 2 random (within constraints) routes that cross 
	 *
	 * @return {array}
	 */
	public function generateCrossRoads(){

		$startTime = microtime(true);

		// Select a random point on the east edge of the map
		$startY = rand(0, $this->height);
		$startPoint = new Point( 0, $startY );

		// Select the inverted equivilent on the west side the map
		$endY = intval($this->height - $startPoint->y);
		$endPoint = new Point( $this->width, $endY );

		$arrPoints = $this->generateRoute( $startPoint, $endPoint );

		$routeID = $this->saveRouteInDB( $arrPoints );
		$this->arrRoutes[] = new Route( $routeID, $arrPoints );


		// Repeat for the north and south edge
		$startX = rand(0, $this->width);
		$startPoint = new Point( $startX, 0 );

		$endX = intval($this->width - $startPoint->x);
		$endPoint = new Point( $endX, $this->height );

		$arrPoints = $this->generateRoute( $startPoint, $endPoint );

		$routeID = $this->saveRouteInDB( $arrPoints );
		$this->arrRoutes[] = new Route( $routeID, $arrPoints );



		

		$arrResult['success'] = true;
		$arrResult['arrPaths'] = [];
		$arrResult['arrPaths'][0] = $this->arrRoutes[0]->getPath();
		$arrResult['arrPaths'][1] = $this->arrRoutes[1]->getPath();
		

		$arrResult['executionTime'] = microtime(true) - $startTime;

		return $arrResult;
	}




	/**
	 * Returns an array of points between a start and end point
	 *
	 * @param {Point} $startPoint
	 * @param {Point} $endPoint
	 *
	 * @return {array} Array of points
	 */
	private function generateRoute( Point $startPoint, Point $endPoint ){

		$objMath = new Math();

		$arrPoints = [];

		// Set the first item as the start point
		$arrPoints[] = $startPoint;

		$routeSectionLength = 50;

		// Walk a varying path between start and end points
		$pointer = 0;
		$distanceLeft = $objMath->distanceBetween( $arrPoints[$pointer], $endPoint );

		while( $distanceLeft > $routeSectionLength ){

			$percentageStep = round( $routeSectionLength / $distanceLeft * 100, 2 );

			$pointer++;

			$perfectPoint = $objMath->pointPercentageBetweenPoints( $arrPoints[$pointer-1], $endPoint, $percentageStep );

			$arrPoints[$pointer] = $perfectPoint->randomVary();

			// Update how much distance left until we reach the endPoint
			$distanceLeft = $objMath->distanceBetween( $arrPoints[$pointer], $endPoint );

		}

		// Finally, add the $endPoint here
		$arrPoints[] = $endPoint;

		return $arrPoints;
	}




	/**
	 * Saves path in database and returns the newly created database ID
	 *
	 * @param {array} $arrPoints Array of instances of Point class
	 *
	 * @return {integer} Database ID of newly saved row
	 */
	private function saveRouteInDB( $arrPoints ){
		
		include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

		$qry = $db->prepare("	INSERT INTO `route` ( `map_id` )
								VALUES ( :mapID );
							");
		$qry->bindValue('mapID', $this->id, PDO::PARAM_INT);
		$qry->execute();
		$routeID = $db->lastInsertId();

		$cnt = 1;
		foreach( $arrPoints as $thisPoint ){
			$qry = $db->prepare("	INSERT INTO `point` ( `route_id`, `order`, `x`, `y` )
									VALUES 	( :routeID, :order, :x, :y );
								");
			$qry->bindValue('routeID', 	$routeID, 			PDO::PARAM_INT);
			$qry->bindValue('order', 	$cnt++, 			PDO::PARAM_INT);
			$qry->bindValue('x', 		$thisPoint->x, 	PDO::PARAM_INT);
			$qry->bindValue('y', 		$thisPoint->y, 	PDO::PARAM_INT);
			$qry->execute();
		}
		$qry->closeCursor();

		return $routeID;
	}


}

