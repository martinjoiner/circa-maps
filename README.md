
# Circa Maps

Generative design code to produce large map-like artworks. Database-backed for saving and efficient collision detection during generation by limiting run-time objects to a section of the map. 


![Photographed segment of a print by Ashley Thomas](/docs/Segment-of-AT-Print.jpg)

The above image is a section of a print by Ashley Thomas. The inspiration for this project is to explore the question of what a code version of Ashley's very slow manual process could look like? 


![Screenshot of Circa at end of 2015](/docs/screenshot-2015.jpg)

Above: Image shows the level of complexity that the project was able to generate at end of 2015


![Example from February 2016](/docs/2016-02-20-Fourth-Map.jpg)

Above: Example from February 2016

Live at: http://circa.butterscotchworld.co.uk



## Target Goal

The end goal is to generate massively detailed SVG files (millions of properties and routes) which can be loaded into a CNC router which will carve the shapes into a 16 ft plywood printing block. This can then be inked-up and pressed using a ride-on road roller. This will be a public event of interest to print and art enthusiasts. 



## Technology 

MySQL, PHP >5.4, Javascript, jQuery, SVG, CSS

In this version the Javascript is used to send AJAX requests for map events and then update the visible SVG in return. Events can cause changes in the data such as new roads, new properties, properties expanding by acquiring neighbouring property, structural growth, destruction or replacement. Each event goes with various parameters such as economic conditions which will affect size and decedance of structure and public or private dominence which will affect variety of building. PHP does all the generation of items on the map server-side with careful collision detection. 



## References

Scalable Vector Graphics (SVG) 1.1 (Second Edition) http://www.w3.org/TR/SVG/

Coordinate Geometry http://www.mathopenref.com/coordintersection.html 
