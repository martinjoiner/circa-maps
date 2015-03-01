<?php

class Property{

	var $id;
	var $arrPoints = array();

	var $arrVerticesX = array(); // An array of just the x values (Useful for basic sanity checking in collision detection)
	var $arrVerticesY = array(); // An array of just the y values


	function __construct( $id, $arrPoints ){

		$this->id 			= $id;
		$this->arrPoints 	= $arrPoints;

		foreach( $arrPoints as $thisPoint ){
			$this->arrVerticesX[] = $thisPoint['x'];
			$this->arrVerticesY[] = $thisPoint['y'];
		}

	}




	public function printMarkup(){
		$html = '<path class="Property" d="M ';
		foreach( $this->arrPoints as $thisPoint ){
			$html .= $thisPoint['x'] . ',' . $thisPoint['y'] . ' ';
		} 
		$html .= 'z" id="property' . $this->id . '" />';
		return $html;	
	}




	/**
	 Returns the central point of the property
	*/
	public function getCenter(){

		$objMath = new Math();
		$firstAv = $objMath->midPoint( $this->arrPoints[0], $this->arrPoints[2] );
		$secondAv = $objMath->midPoint( $this->arrPoints[1], $this->arrPoints[3] );
		return $objMath->midPoint( $firstAv, $secondAv );
	}




	/** 
	 Returns an associative array with 'class', 'd' values 
	 The front of a property is the 
	*/
	public function getFront(){
		$arrFront = array();
		$arrFront['class'] = 'Front';
		$arrFront['d'] = 'M ' . $this->arrPoints[0]['x'] . ',' . $this->arrPoints[0]['y'] . ' ' . $this->arrPoints[1]['x'] . ',' . $this->arrPoints[1]['y'];
		return $arrFront;
	}


}
