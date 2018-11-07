<?php

namespace App;

class Path {

    public $points = [];

    public function __construct(array $points)
    {
        $this->points = $points;
    }


    /**
     * Returns the XML markup of the path
     */
    public function markup(): string
    {
        $path = $this->getPath();
        $html = "\t<path d=\"" . $path['d'] . "\" />\n";
        return $html;
    }




    /**
     * Returns an associative array containing 'd' or 'points'
     *
     * @param string $format - Can be 'd' or 'points'
     *
     * @return array
     */
    public function getPath( $format = 'd' )
    {
        $path = [];

        if( $format === 'd' ){
            // Return a M...d string for rendering SVG
            $path['d'] = 'M ';
            foreach ($this->points as $point) {
                $path['d'] .= $point->x . ',' . $point->y . ' ';
            }
            $path['d'] .= 'z';
        } else if ( $format === 'points' ){
            // Return the co-ordinates for rendering in 3D
            $path['points'] = $this->points;
        }

        return $path;
    }

}
