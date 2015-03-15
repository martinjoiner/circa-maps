<?php

/**
 Class for deleting properties
*/
class PropertyDeleter extends MapSection{




	/**
	 Deletes any properties that occupy $x $y
	*/
	function deleteProperties( $x, $y ){

		$objMath = new Math();

		$arrPropertyIDs = array();

		foreach( $this->arrProperties as $thisProperty ){

			$points_polygon = count($thisProperty->arrPoints);  // number vertices - zero-based array

			if( $objMath->isInPolygon($points_polygon, $thisProperty->arrVerticesX, $thisProperty->arrVerticesY, $x, $y) ){
				$arrPropertyIDs[] = $thisProperty->id;
			}

		}

		$this->deleteFromDB( $arrPropertyIDs );

		return $arrPropertyIDs;
	}




	/**
	 Deletes the row from the database
	*/
	public function deleteFromDB( $arrPropertyIDs ){

		// Call the saveInDB method of the newly created Property 
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
