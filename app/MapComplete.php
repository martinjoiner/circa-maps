<?php

namespace App;

use App\Map;
use PDO;

/** 
 * MapComplete is the entire map! Memory intensive.
 * Only instantiate if you genuinely need to perform operations on the whole area 
 */
class MapComplete extends Map {


	/** {integer} Database ID of the map */
	protected $id;


	/** 
	 * @constructor 
	 *
	 * @param {integer} $id 
	 */
	public function __construct( $id ){

		$this->id = $id;

		parent::extractMapFromDB();

		$this->extractRoutesFromDB();

		$this->extractPropertiesFromDB();
		
	}




	/**
	 * Extracts all the data for the routes on this map 
	 */
	protected function extractRoutesFromDB(){

		include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

		$qry = $db->prepare("	SELECT 		`route`.`id`, `x`, `y`
								FROM 		`map`
								LEFT JOIN 	`route` ON `route`.`map_id` = `map`.`id`
								LEFT JOIN 	`point` ON `point`.`route_id` = `route`.`id`
								WHERE 		`map`.`id` = :mapID 
								ORDER BY 	`route`.`id`, `point`.`order` 
							");
		$qry->bindValue('mapID', $this->id, PDO::PARAM_INT);
		$qry->execute();
		$rslt = $qry->fetchAll(PDO::FETCH_ASSOC);
		$qry->closeCursor();

		parent::processDBResult( $rslt, 'ROUTE');

	}	




	/**
	 * Extracts all the data for the properties on this map 
	 */
	private function extractPropertiesFromDB(){

		include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

		$qry = $db->prepare("	SELECT 		`property`.`id`, `x`, `y`
								FROM 		`map`
								LEFT JOIN 	`property` ON `property`.`map_id` = `map`.`id`
								LEFT JOIN 	`point` ON `point`.`property_id` = `property`.`id`
								WHERE 		`map`.`id` = :mapID 
								ORDER BY 	`property`.`id`, `point`.`order` 
							");
		$qry->bindValue('mapID', $this->id, PDO::PARAM_INT);
		$qry->execute();
		$rslt = $qry->fetchAll(PDO::FETCH_ASSOC);
		$qry->closeCursor();

		parent::processDBResult( $rslt, 'PROPERTY');

	}




	/**
	 * Produces markup for an SVG image file
	 *
	 * @param {boolean} $includeRoutes Dictates whether the routes are included
	 * @param {boolean} $includeProperties Dictates whether the properties are included
	 *
	 * @return {string} XML markup 
	 */
	public function printFileMarkup( $includeRoutes = true, $includeProperties = true ){
		$xml = "<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\" \"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">\n";
		$xml .= $this->printMarkup( $includeRoutes, $includeProperties );
		return $xml;
	}




	/**
	 * Produces markup for an SVG image inside HTML document
	 *
	 * @param {boolean} $includeRoutes Dictates whether the routes are included
	 * @param {boolean} $includeProperties Dictates whether the properties are included
	 *
	 * @return {string} XML markup 
	 */
	public function printMarkup( $includeRoutes = true, $includeProperties = true ){

		$xml = "<svg id=\"svgMap\" xmlns=\"http://www.w3.org/2000/svg\" width=\"" . $this->width . "\" height=\"" . $this->height . "\" viewBox=\"0 0 " . $this->width . " " . $this->height . "\" enable-background=\"new 0 0 " . $this->width . " " . $this->height . "\">\n";

		$xml .= "<style type=\"text/css\"><![CDATA[
					.Route, .Property{ stroke: #555; stroke-opacity: 1;  }
				    .Route{ fill: none; stroke-linejoin: round; }
				    .Property{ fill: #777; opacity: 0.5; stroke-width: 0; }
				    .Front{ stroke: #E22; }
				    .DebugPath{ fill-opacity: 0.5; }
				  ]]></style>\n";

		if( $includeRoutes ){
			$xml .= "<g class=\"routes\">\n";
			foreach( $this->arrRoutes as $thisRoute ){
				$xml .= $thisRoute->printMarkup();
			}
			$xml .= "</g>\n";
		}		

		if( $includeProperties ){
			$xml .= "<g class=\"properties\">\n";
			foreach( $this->arrProperties as $thisProperty ){
				$xml .= $thisProperty->printMarkup();
			}
			$xml .= "</g>\n";
		}

		// Add a group for debugging paths
		$xml .= "<g class=\"debug\"></g>\n";

		$xml .= "</svg>\n";

		return $xml;	

	}


	public function getWidth(){
		return $this->width;
	}

	public function getHeight(){
		return $this->height;
	}


}

