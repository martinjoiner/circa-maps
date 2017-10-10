<?php

namespace App;

class PropertyRelativePoint{

	/** {integer} The side the intersection occurs on: Either 1 or 2 */
	public $intersectSideNum;

	/** {float} The position of the intersection as a percentage of the intersected side */
	public $intersectOnSidePercentage = 0;

	/** {float} The position of the point as a percentage between the property's first point and the intersect point */
	public $pointPercentage = 0;




	/**
	 * Takes a Property and an absolute point and saves the elements of an equation to produce the equivilent point on any other property
	 * See red notebook page 24 Feb 2016
	 *
	 * @param {Property} $property
	 * @param {Point} $point
	 */
	public function __construct( Property $property, Point $point ){

		// Firstly, check the point is actually inside the Property
		if( !$property->coversPoint($point) ){
			return false;
		}

		// Make a segment of the property's first point and the point we are converting
		$firstPointToPointSegment = [ $property->arrPoints[0], $point ];

		// Get equation of line of segment between property's first point and the point
		$sideIntersectResult = CoordinateGeometry::lineSegmentIntersectionPoint( $firstPointToPointSegment, $property->getSide(1) );

		if( $sideIntersectResult['intersectionOnSegment'] == 'B' ){
			$this->intersectSideNum = 1;
		} else {
			$sideIntersectResult = CoordinateGeometry::lineSegmentIntersectionPoint( $firstPointToPointSegment, $property->getSide(2) );
			$this->intersectSideNum = 2;
		}

		$intersectSide = $property->getSide($this->intersectSideNum);

		// Calculate the intersection point on the side as a percentage
		$intersectDistance = Math::distanceBetween( $intersectSide[0], $sideIntersectResult['point'] );
		$sideLength = Math::distanceBetween( $intersectSide[0], $intersectSide[1] );
		$this->intersectOnSidePercentage = ( $intersectDistance / $sideLength ) * 100;

		// Calculate the point we are converting as a percentage on line between Property's first point and the intersection point
		$propertyFirstPointToPointDistance = Math::distanceBetween( $property->arrPoints[0], $point );
		$propertyFirstPointToIntersectDistance = Math::distanceBetween( $property->arrPoints[0], $sideIntersectResult['point'] );
		$this->pointPercentage = ( $propertyFirstPointToPointDistance / $propertyFirstPointToIntersectDistance ) * 100;

		return $arrResult;

	}




	/**
	 * Produces an absolute point based on a given property
	 * 
	 * @param {Property} $property
	 *
	 * @return {Point}
	 */
	public function absolutePoint( Property $property ){

		$intersectSide = $property->getSide( $this->intersectSideNum );
		$intersectOnSidePoint = Math::pointPercentageBetweenPoints( $intersectSide[0], $intersectSide[1], $this->intersectOnSidePercentage );

		return Math::pointPercentageBetweenPoints( $property->arrPoints[0], $intersectOnSidePoint, $this->pointPercentage );;
	}

}
