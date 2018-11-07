<?php

namespace App;

use App\Helpers\Transposer;

/**
 * A complicated SVG describing an architectural structure such as a building
 */
class Structure {

    public $id = 'structure'; // Temporarily hard-coded until database-stored

    /** @var Property - A generic hypothetical property upon which the structure was designed. */
    public $baseProperty;

    /** @var array of instances of Path */
    public $components = [];


    public function __construct(array $components = [], Property $baseProperty = null)
    {
        $this->components = $components;

        $this->baseProperty = $baseProperty;
    }




    /**
     * @param $destinationProperty
     * @return Structure
     */
    public function transposeOnProperty(Property $destinationProperty): Structure
    {
        $components = [];
        foreach($this->components as $path) {
            $points = [];
            foreach($path->points as $point) {
                $points[] = Transposer::transposePoint($this->baseProperty, $point, $destinationProperty);
            }
            $components[] = new Path($points);
        }
        return new Structure($components);
    }


    /**
     * @param Property $property
     * @return string
     */
    public function markupOnProperty(Property $property): string
    {
        $transposedStructure = $this->transposeOnProperty($property);

        return $transposedStructure->markup();
    }




    /**
     * Returns the XML markup of the components (paths)
     */
    public function markup(): string
    {
        $html = '';

        foreach ($this->components as $path) {
            $html .= $path->markup();
        }

        return $html;
    }


}
