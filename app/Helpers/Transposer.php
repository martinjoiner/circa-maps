<?php

namespace App\Helpers;

use App\Property;
use App\Point;
use App\Math;
use App\CoordinateGeometry;

class Transposer {


    public static function transposePoint(Property $baseProperty, Point $point, Property $destinationProperty): Point
    {
        // Measure x diff between first point of property and point
        $xDiff = $point->x - $baseProperty->arrPoints[0]->x;

        // Get x diff as percentage of length of first side
        $xDiffPerc = $xDiff / Math::distanceBetween($baseProperty->arrPoints[0], $baseProperty->arrPoints[1]);

        // Measure y diff between first point of property and point
        $yDiff = $point->y - $baseProperty->arrPoints[0]->y;

        // Get y diff as percentage of fourth side
        $yDiffPerc = $yDiff / Math::distanceBetween($baseProperty->arrPoints[0], $baseProperty->arrPoints[3]);


        // Now transpose

        // Find virtual x-line
        $firstSideIntersect = Math::pointPercentageBetweenPoints($destinationProperty->arrPoints[0],$destinationProperty->arrPoints[1],$xDiffPerc*100);
        $thirdSideIntersect = Math::pointPercentageBetweenPoints($destinationProperty->arrPoints[3],$destinationProperty->arrPoints[2],$xDiffPerc*100);
        $virtualX = [ $firstSideIntersect, $thirdSideIntersect ];

        // Find virtual y-line
        $secondSideIntersect = Math::pointPercentageBetweenPoints($destinationProperty->arrPoints[1],$destinationProperty->arrPoints[2],$yDiffPerc*100);
        $fourthSideIntersect = Math::pointPercentageBetweenPoints($destinationProperty->arrPoints[0],$destinationProperty->arrPoints[3],$yDiffPerc*100);
        $virtualY = [ $secondSideIntersect, $fourthSideIntersect ];

        $result = CoordinateGeometry::lineSegmentIntersectionPoint($virtualX,$virtualY);

        return $result['point'];
    }


}
