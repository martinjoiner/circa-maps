<?php

namespace App;

use PDO;
use \Exception;

class Maps {


	/** 
	 * Returns an array of all the maps 
	 */
	public function all(){

		include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

		$qry = $db->prepare("	SELECT `id`, `name`, `width`, `height` 
								FROM `map`
								ORDER BY `id`  
							");
		$qry->execute();
		$maps = $qry->fetchAll(PDO::FETCH_ASSOC);
		$qry->closeCursor();

		if( !$maps ){
			throw new Exception('No maps found');
		}

		foreach( $maps as &$map ){
			$map['id'] = intval($map['id']);
			$map['name'] = $map['name'];
			$map['width'] = intval($map['width']);
			$map['height'] = intval($map['height']);
		}

		return $maps;
	}


}
