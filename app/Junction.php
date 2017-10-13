<?php

namespace App;

use JsonSerializable;
use App\Point;
use App\Route;

/** An intersection of 2 routes */
class Junction implements JsonSerializable {

    /** $key is a combination of the letter 'j' followed by the lowest route ID, a hyphen and the highest route ID */
    private $key;

    /** Instance of App\Point representing the point where they intersect */
    private $point;

    private $segmentA;
    private $segmentB;

    /** The Routes */
    private $routeA; // <-- Always the lowest ID route
    private $routeB;

    public function __construct( $intersection, Route $routeA, Route $routeB )
    { 
        $this->point = $intersection['point'];
        $this->segmentA = $intersection['segmentA'];
        $this->segmentB = $intersection['segmentB'];

        $this->key = SELF::makeKey($routeA, $routeB);
        if( $routeA->getId() < $routeB->getId() ){
            $this->routeA = $routeA;
            $this->routeB = $routeB;
        } else if( $routeB->getId() < $routeA->getId() ){
            $this->routeA = $routeB;
            $this->routeB = $routeA;
        } else {
            // They are the same route!
            throw new Exception('A route cannot intersect itself!');
        }
    }

    /**
     * The letter 'j' followed by the lowest route ID, a hyphen and the highest route ID
     *
     * @param App\Route $routeA
     * @param App\Route $routeB
     */
    public static function makeKey( Route $routeA, Route $routeB ){
        $key = 'j';
        if( $routeA->getId() < $routeB->getId() ){
            $key .= $routeA->getId() . '-' . $routeB->getId();
        } else {
            $key .= $routeB->getId() . '-' . $routeA->getId();
        }
        return $key;
    }
    
    public function getKey()
    {
        return $this->key;
    }
    
    public function getPoint()
    {
        return $this->point;
    }
    
    public function getRouteA()
    {
        return $this->routeA;
    }
    
    public function getRouteB()
    {
        return $this->routeB;
    }

    /**
     * Because this class implements JsonSerializable we can provide this method to describe how it gets serialised
     */
    public function jsonSerialize() {
        return [
            'key' => $this->key,
            'point' => $this->point,
            'segmentA' => $this->segmentA,
            'segmentB' => $this->segmentB,
            'routeA_id' => $this->routeA->getId(),
            'routeB_id' => $this->routeB->getId()
        ];
    }

}
