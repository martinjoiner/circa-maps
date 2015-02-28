<?php

class Path{

	var $id = '';
	var $d = '';
	var $arrVerticesX;
	var $arrVerticesY;
	var $arrPoints;

	function __construct( $id = null, $d = null ){

		$this->id = $id;
		$this->d = $d;

		$pregVerticesX;
		$pregVerticesY;

		$pregPoints;
		preg_match_all('/ ([0-9.]*,[0-9.]*)/', $d, $pregPoints);
		$this->arrPoints = $pregPoints[1];

		preg_match_all('/ ([0-9.]*),[0-9.]*/', $d, $pregVerticesX);
		preg_match_all('/ [0-9.]*,([0-9.]*)/', $d, $pregVerticesY);
		$this->arrVerticesX = $pregVerticesX[1];
		$this->arrVerticesY = $pregVerticesY[1];

	}

	public function printMarkup(){
		$html = '<path style="fill:#888;stroke:#000000;stroke-width:1px;stroke-linecap:butt;stroke-linejoin:miter;stroke-opacity:1"
				       d="' . $this->d . '"
				       id="path' . $this->id . '" />';
		return $html;	

	}

	public function getCenter(){

		$objMath = new Math();
		$firstAv = $objMath->midPoint( $this->arrPoints[0], $this->arrPoints[2] );
		$secondAv = $objMath->midPoint( $this->arrPoints[1], $this->arrPoints[3] );
		return $objMath->midPoint( $firstAv, $secondAv );
	}

}

?>