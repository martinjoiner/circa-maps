<?php

/**
 Class for deleting properties
*/
class PropertyDeleter extends MapSection{




	/**
	 * Deletes any properties that occupy the given point
	 *
	 * @param {Point} $point
	 *
	 * @return {array} of IDs of deleted properties (used to update the DOM interface)
	 */
	public function deleteProperties( Point $point ){

		$arrPropertyIDs = [];

		foreach( $this->arrProperties as $thisProperty ){

			$points_polygon = count($thisProperty->arrPoints);  // number vertices - zero-based array

			if( Math::isInPolygon($points_polygon, $thisProperty->arrVerticesX, $thisProperty->arrVerticesY, $point) ){
				$arrPropertyIDs[] = $thisProperty->id;
			}

		}

		$this->deleteFromDB( $arrPropertyIDs );

		return $arrPropertyIDs;
	}




	/**
	 * Deletes the row from the database
	 *
	 * @param {array} IDs of properties to delete
	 */
	private function deleteFromDB( array $arrPropertyIDs ){

		include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

		$qry = $db->prepare("	DELETE FROM `property` 
								WHERE `id` IN ( " . implode( ', ', $arrPropertyIDs) . " )
								AND `map_id` = :mapID 
							");
		$qry->bindValue('mapID', $this->id, PDO::PARAM_INT);

		$qry->execute();
		$qry->closeCursor();

	}


	
}
