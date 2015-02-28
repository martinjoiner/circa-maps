<?php

class Route{

	var $id = 0;
	var $width = 4;
	var $arrPoints = array();

	function __construct( $id, $arrPoints ){

		$this->id 			= $id;
		$this->arrPoints 	= $arrPoints;

	}

	/**
	 Returns the markup representation of this path for inclusion in an SVG file 
	*/
	function printMarkup(){
		$html = '<path class="Route" style="stroke-width:' . $this->width . 'px;" d="M ';
		foreach( $this->arrPoints as $thisPoint ){
			$html .= $thisPoint['x'] . ',' . $thisPoint['y'] . ' ';
		} 

		$html .= '" id="route' . $this->id . '" />
		';
		return $html;
	}



	/**
	 Walks the route, totallying up the distance between each point to get a total length
	*/
	function calculateLength(){

	}


}
