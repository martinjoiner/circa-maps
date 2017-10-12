<?php

namespace App;

use PDO;

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

		foreach( $this->arrProperties as $property ){

			if( $property->coversPoint( $point ) ){
				$arrPropertyIDs[] = $property->id;
			}

		}

		return $this->deleteFromDB( $arrPropertyIDs );
	}




	/**
	 * Deletes all properties on this map (map section)
	 *
	 * @return {array} of IDs of deleted properties 
	 */
	public function cleanseAll(){

		$arrPropertyIDs = [];

		foreach( $this->arrProperties as $property ){
			$arrPropertyIDs[] = $property->id;
		}

		return $this->deleteFromDB( $arrPropertyIDs );
	}




	/**
	 * Deletes the row from the database
	 *
	 * @param {array} IDs of properties to delete
	 *
	 * @return {array} The same array passed in
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

		return $arrPropertyIDs;
	}


	
}
