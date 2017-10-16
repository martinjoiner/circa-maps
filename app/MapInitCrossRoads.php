<?php

namespace App;

use App\MapComplete;
use App\Math;
use PDO;

/**
 * This is the very beginning of a settlement. It creates 2 routes that intersect. From there civilisation can begin
 */
class MapInitCrossRoads extends MapComplete {

    const ROUTE_SEGMENT_LENGTH = 50;

    /** The maximum distance a point can vary **/
    const MAX_POINT_VARY = 12;



    /**
     * If the map contains no routes, this function draws 2 randomly varied routes that cross.
     * One from top to bottom, the other from left to right.
     *
     * @return {array}
     */
    public function generateCrossRoads(){

        $result = ['success' => false];

        if( count($this->arrRoutes) > 0 ){
            $result['message'] = 'Could not initialise X-roads because routes already exist';
            return $result;
        }

        $startTime = microtime(true);

        // Select a random point on the east edge of the map
        $startY = rand(0, $this->height);
        $startPoint = new Point( 0, $startY );

        // Select the inverted equivilent on the west side the map
        $endY = intval($this->height - $startPoint->y);
        $endPoint = new Point( $this->width, $endY );

        $arrPoints = $this->generateRoute( $startPoint, $endPoint );

        $routeID = $this->saveRouteInDB( $arrPoints );
        $this->arrRoutes[] = new Route( $routeID, $arrPoints );


        // Repeat for the north and south edge
        $startX = rand(0, $this->width);
        $startPoint = new Point( $startX, 0 );

        $endX = intval($this->width - $startPoint->x);
        $endPoint = new Point( $endX, $this->height );

        $arrPoints = $this->generateRoute( $startPoint, $endPoint );

        $routeID = $this->saveRouteInDB( $arrPoints );
        $this->arrRoutes[] = new Route( $routeID, $arrPoints );



        

        $result['success'] = true;
        $result['arrPaths'] = [
            $this->arrRoutes[0]->getPath(),
            $this->arrRoutes[1]->getPath()
        ];
        

        $result['executionTime'] = microtime(true) - $startTime;

        return $result;
    }




    /**
     * Returns an array of points between a start and end point
     *
     * @param {Point} $startPoint
     * @param {Point} $endPoint
     *
     * @return {array} Array of points
     */
    private function generateRoute( Point $startPoint, Point $endPoint ){

        $points = [];

        // Set the first item as the start point
        $points[] = $startPoint;

        // Walk a varying path between start and end points
        $pointer = 0;
        $distanceLeft = Math::distanceBetween( $points[$pointer], $endPoint );

        while( $distanceLeft > SELF::ROUTE_SEGMENT_LENGTH ){

            $percentageStep = round( SELF::ROUTE_SEGMENT_LENGTH / $distanceLeft * 100, 2 );

            $pointer++;

            $perfectPoint = Math::pointPercentageBetweenPoints( $points[$pointer-1], $endPoint, $percentageStep );

            $points[$pointer] = $perfectPoint->randomVary(SELF::MAX_POINT_VARY);

            // Update how much distance left until we reach the endPoint
            $distanceLeft = Math::distanceBetween( $points[$pointer], $endPoint );

        }

        // Finally, add the $endPoint here
        $points[] = $endPoint;

        return $points;
    }




    /**
     * Saves path in database and returns the newly created database ID
     *
     * @param {array} $arrPoints Array of instances of Point class
     *
     * @return {integer} Database ID of newly saved row
     */
    private function saveRouteInDB( $arrPoints ){
        
        include( $_SERVER['DOCUMENT_ROOT'] . '/db_connect.inc.php' );

        $qry = $db->prepare("   INSERT INTO `route` ( `map_id` )
                                VALUES ( :mapID );
                            ");
        $qry->bindValue('mapID', $this->id, PDO::PARAM_INT);
        $qry->execute();
        $routeID = $db->lastInsertId();

        $cnt = 1;
        foreach( $arrPoints as $thisPoint ){
            $qry = $db->prepare("   INSERT INTO `point` ( `route_id`, `order`, `x`, `y` )
                                    VALUES  ( :routeID, :order, :x, :y );
                                ");
            $qry->bindValue('routeID',  $routeID,           PDO::PARAM_INT);
            $qry->bindValue('order',    $cnt++,             PDO::PARAM_INT);
            $qry->bindValue('x',        $thisPoint->x,  PDO::PARAM_INT);
            $qry->bindValue('y',        $thisPoint->y,  PDO::PARAM_INT);
            $qry->execute();
        }
        $qry->closeCursor();

        return $routeID;
    }


}

