<?php

class Route{

	var $id = 0;
	var $width = 14;
	var $arrPoints = array();

	function __construct( $id, $arrPoints ){

		$this->id 			= $id;
		$this->arrPoints 	= $arrPoints;

	}




	/**
	 Returns the markup representation of this path for inclusion in an SVG file 
	*/
	public function printMarkup(){
		$arrPath = $this->getPath();
		$html = '<path class="' . $arrPath['class'] . '" stroke-width="' . $arrPath['stroke-width'] . '" d="' . $arrPath['d'] . '" id="' . $arrPath['id'] . '" />';
		return $html;	
	}




	/**
	 Returns an associative array with 'class', 'id' and 'd' values
	*/
	public function getPath(){
		$arrPath = array();
		$arrPath['id'] = 'route' . $this->id;
		$arrPath['class'] = 'Route';
		$arrPath['stroke-width'] = $this->width;
		$arrPath['d'] = 'M ';
		foreach( $this->arrPoints as $thisPoint ){
			$arrPath['d'] .= $thisPoint['x'] . ',' . $thisPoint['y'] . ' ';
		} 
		return $arrPath;
	}




	/**
	 Walks the route, totallying up the distance between each point to get a total length
	*/
	function calculateLength(){

	}


}
