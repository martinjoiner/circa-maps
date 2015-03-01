<?php

class MapComplete extends Map{



	function __construct( $id ){

		$this->id = $id;

		parent::extractMapFromDB();

		$this->extractRoutesFromDB();

		$this->extractPropertiesFromDB();
		
	}




	/**
	 Extracts all the data for the routes on this map 
	*/
	private function extractRoutesFromDB(){

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
	 Extracts all the data for the properties on this map 
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
	 Returns HTML markup of an svg. 
	 $includeRoutes boolean dictates whether the routes are included
	 $includeProperties boolean dictates whether the properties are included
	*/
	public function printMarkup( $includeRoutes = true, $includeProperties = true ){
		$html = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $this->width . '" height="' . $this->height . '" id="svgMap">
				';

		$html .= '<style type="text/css"><![CDATA[
					.Route, .Property{ stroke: #555; stroke-opacity: 1;  }
				    .Route{ fill: none; stroke-linejoin: round; }
				    .Property{ fill: #777; stroke-width: 1; }
				  ]]></style>';

		if( $includeRoutes ){
			foreach( $this->arrRoutes as $thisRoute ){
				$html .= $thisRoute->printMarkup();
			}
		}		

		if( $includeProperties ){
			foreach( $this->arrProperties as $thisProperty ){
				$html .= $thisProperty->printMarkup();
			}
		}

		$html .= '</svg>
		';
		return $html;	

	}


}

