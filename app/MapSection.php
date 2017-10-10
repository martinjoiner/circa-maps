<?php

namespace App;

use PDO;

/**
 * MapSection is a limited area of the map with only the routes and properties in proximity to given coordinates
 */
class MapSection extends Map {

	private $topLeftPoint;

	private $bottomRightPoint;


	/**
	 *
	 */
	public function __construct( $id, $x, $y, $xMargin = 100, $yMargin = 100 ){

		$this->id = $id;

		parent::extractMapFromDB();

		$xMin = parent::limitXToBoundaries( $x - $xMargin );
		$xMax = parent::limitXToBoundaries( $x + $xMargin );
		$yMin = parent::limitYToBoundaries( $y - $yMargin );
		$yMax = parent::limitYToBoundaries( $y + $yMargin );

		$this->topLeftPoint = new Point($xMin, $yMin);
		$this->bottomRightPoint = new Point($xMax, $yMax);

		$this->extractRoutesFromDB();

		$this->extractPropertiesFromDB();
		
	}




	/**
	 * Extracts all the data for the routes on this map inside the xMin, xMax, yMin and yMax boundaries
	 * the reason we do this is because collision detection across the entire set of objects will be impossible when there are thousands of points
	 * By leveraging a database index we can only run collission detection against objects that contain points that are resonably close
	 */
	protected function extractRoutesFromDB(){

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
								GROUP BY `point`.`id`
								ORDER BY 	`route`.`id`, `point`.`order` 
							");
		$qry->bindValue('mapID', $this->id, PDO::PARAM_INT);
		$qry->bindValue('xMin', $this->topLeftPoint->x, PDO::PARAM_INT);
		$qry->bindValue('xMax', $this->bottomRightPoint->x, PDO::PARAM_INT);
		$qry->bindValue('yMin', $this->topLeftPoint->y, PDO::PARAM_INT);
		$qry->bindValue('yMax', $this->bottomRightPoint->y, PDO::PARAM_INT);
		$qry->execute();
		$rslt = $qry->fetchAll(PDO::FETCH_ASSOC);
		$qry->closeCursor();

		parent::processDBResult( $rslt, 'ROUTE' );

	}




	/**
	 * Extracts all the data for the properties on this map inside the xMin, xMax, yMin and yMax boundaries
	 * the reason we do this is because collision detection across the entire set of objects will be impossible when there are thousands of items on the map
	 * By leveraging a database index we can only run collission detection against objects that contain points that are resonably close
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
								GROUP BY `point`.`id` 
								ORDER BY 	`property`.`id`, `point`.`order` 
							");
		$qry->bindValue('mapID', $this->id, PDO::PARAM_INT);
		$qry->bindValue('xMin', $this->topLeftPoint->x, PDO::PARAM_INT);
		$qry->bindValue('xMax', $this->bottomRightPoint->x, PDO::PARAM_INT);
		$qry->bindValue('yMin', $this->topLeftPoint->y, PDO::PARAM_INT);
		$qry->bindValue('yMax', $this->bottomRightPoint->y, PDO::PARAM_INT);
		$qry->execute();
		$rslt = $qry->fetchAll(PDO::FETCH_ASSOC);
		$qry->closeCursor();

		parent::processDBResult( $rslt, 'PROPERTY' );

	}




	/**
	 * Uses the isOccupied() function to find if a property is in this location 
	 * Then returns the result of calling that property's getOffsetPoints() method
	 *
	 * @param {integer} $x
	 * @param {integer} $y
	 *
	 * @return {integer} $y
	 */
	public function getOffsetSides( $x, $y ){
		$arrIsOccupiedResult = $this->isOccupied( $x, $y );
		if( $arrIsOccupiedResult['isOccupied'] ){
			$thisProperty = $this->arrProperties[ $arrIsOccupiedResult['arrPropertiesPointer'] ];
			return $thisProperty->getOffsetSides();
		}
	}




	/**
	 * Uses the isOccupied() function to find if a property is in this location 
	 * Then attempts to improve that property by changing it's shape
	 *
	 * @param {Point} $point
	 *
	 * @return {array} Contains: 'isOccupiedResult', 'cntSidesReplaced', 'path' and {array} 'arrNeighboursOffsetSides'
	 */
	public function improvePropertyAtPoint( Point $point ){

		$arrResult = [ 	'cntSidesReplaced' => 0,
						'isOccupiedResult' => null,
						'path' => null,
						'arrNeighboursOffsetSides' => [],
						'message' => '',
						'cntPotentialImprovements' => 0
					];

		$arrIsOccupiedResult = $this->isOccupied( $point->x, $point->y );

		$arrResult['isOccupiedResult'] = $arrIsOccupiedResult;

		if( !$arrIsOccupiedResult['isOccupied'] ){
			// Return result early, there's nowt we can do :-(
			return $arrResult;
		} else {
			$propertyToBeImproved = $this->arrProperties[ $arrIsOccupiedResult['arrPropertiesPointer'] ];
		}

		$arrNeighboursOffsetSides = array();

		// Gather the offset points of all the neighbouring properties (ie. All but the property in question)
		foreach( $this->arrProperties as $key => $thisProperty ){

			if( $key != $arrIsOccupiedResult['arrPropertiesPointer'] ){
				$arrNeighboursOffsetSides = array_merge( $arrNeighboursOffsetSides, $thisProperty->getOffsetSides() );
			}
		}

		// TODO: Strip out any sides containing occupied points

		// TODO: Strip out any sides contianing points that are too close to a route

		$arrPotentialImprovements = [];

		// Record the property's area before any changes
		$arrAreaDataPreChange = $propertyToBeImproved->getAreaData();

		// Record the property's centerPoint before any changes
		$centerPointPreChange = $propertyToBeImproved->centerPoint();

		// Loop over all 4 sides of the property, testing if replacing them with a neighbour's offset side would be an improvement in area
		for( $i = 0; $i < 4; $i++ ){

			// From the array of potential points, get the 5 nearest that are within 100 units 
			//$arrNearestPoints = Math::nearestPointsInArray( $propertyToBeImproved->arrPoints[$i], $arrNeighboursOffsetSides, 5, 100 );

			// Save the original side in a variable
			$arrSidePreChange = $propertyToBeImproved->getSide($i);

			// Set the best area achieved so far as being the current one
			$bestAreaSoFar = $arrAreaDataPreChange['area'];
			$arrSideBestSoFar = $arrSidePreChange;

			$improvementToSidePossible = false;

			// Loop over the 5 nearest points
			$nLimit = sizeof($arrNeighboursOffsetSides);						
	
			for( $n = 0; $n < $nLimit; $n++ ){

				$validReplacement = true;
				
				// if( $propertyToBeImproved->hasMatchingPoint($arrNearestPoints[$n]) ){
				// 	$validReplacement = false;
				// } else {

				// If replacing this side with the new side will cause the points to cross paths during the switch, 
				// swap the points around so they do not cross
				// Use the doSegmentsIntersect() method to determine if the points cross paths during switch
				$arrReplacementPath1 = array( $arrNeighboursOffsetSides[$n][0], $arrSidePreChange[0] );
				$arrReplacementPath2 = array( $arrNeighboursOffsetSides[$n][1], $arrSidePreChange[1] );
				if( CoordinateGeometry::doSegmentsIntersect( $arrReplacementPath1, $arrReplacementPath2 ) ){
					$arrCorrectedOrientationSide = array_reverse( $arrNeighboursOffsetSides[$n] );
				} else {
					$arrCorrectedOrientationSide = $arrNeighboursOffsetSides[$n];
				}

				// Replace the point with the nearest point from arrNeighboursOffsetSides
				$propertyToBeImproved->replaceSide( $i, $arrCorrectedOrientationSide );

				// Check the original centerPoint is still covered by the new property after changes (ie. It hasn't turned inside out)
				if( !$propertyToBeImproved->coversPoint( $centerPointPreChange ) ){
					$validReplacement = false;
				}

				// Get info on the new shape of property
				$arrPostChangeInfo = $propertyToBeImproved->getInfo();
				if( !$arrPostChangeInfo['isStandard'] ){
					$validReplacement = false;
				}

				if( $validReplacement && parent::isCollisionWithMapProperties( $propertyToBeImproved ) ){
					$validReplacement = false;
				}

				if( $validReplacement && parent::isCollisionWithMapRoutes( $propertyToBeImproved ) ){
					$validReplacement = false;
				}

				// Test to see if your area has increased 
				if( $validReplacement && $arrPostChangeInfo['arrAreaData']['area'] > $bestAreaSoFar ){
					
					$improvementToSidePossible = true;
					$bestAreaSoFar = $arrPostChangeInfo['arrAreaData']['area'];
					$arrSideBestSoFar = $arrCorrectedOrientationSide;
				} 

			}

			// If it has improved, record this replacement as a potential improvement
			if( $improvementToSidePossible ){
				$arrPotentialImprovements[] = array( "numSide"=>$i, "area"=>$bestAreaSoFar, "arrSideNew"=>$arrSideBestSoFar );
			} 
			
			// Revert the change for now because we gonna try the other sides
			$propertyToBeImproved->replaceSide( $i, $arrSidePreChange );

		}


		
		// Apply the improvement with the biggest gain
		if( sizeof($arrPotentialImprovements) ){

			$arrResult['cntPotentialImprovements'] = count($arrPotentialImprovements);

			// Sort $arrPotentialImprovements by "area" to see which replacement makes the biggest gain
			usort( $arrPotentialImprovements, array( $this, "compareImprovements") );

			// Apply the first item in the arrPotentialImprovements array which will by definition be the best
			$propertyToBeImproved->replaceSide( $arrPotentialImprovements[0]['numSide'], $arrPotentialImprovements[0]['arrSideNew'] );

			$arrResult['cntSidesReplaced']++;

			$arrResult['message'] .= 'Replaced side ' . $arrPotentialImprovements[0]['numSide'];

			// Save the new points in database
			$propertyToBeImproved->saveInDB();
		}

		$arrResult['path'] = $propertyToBeImproved->getPath();
		$arrResult['arrNeighboursOffsetSides'] = $arrNeighboursOffsetSides;

		return $arrResult;
	}




	/**
	 * Comparison function used by the method above
	 *
	 * @param {array} $arrImprovement1
	 * @param {array} $arrImprovement2
	 *
	 * @return {integer} 0 or 1
	 */
	private static function compareImprovements( $arrImprovement1, $arrImprovement2 ){
		if( $arrImprovement1['area'] < $arrImprovement2['area'] ){
			return 1;
		} 
		return 0;
	}



}

