<?php

class Property{

	var $id;
	var $arrPoints;

	var $arrVerticesX;
	var $arrVerticesY;


	function __construct( $id, $arrPoints ){

		$this->id 			= $id;
		$this->arrPoints 	= $arrPoints;

	}




	public function printMarkup(){
		$html = '<path class="Property" d="M ';
		foreach( $this->arrPoints as $thisPoint ){
			$html .= $thisPoint['x'] . ',' . $thisPoint['y'] . ' ';
		} 
		$html .= ' z" id="property' . $this->id . '" />';
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

}
