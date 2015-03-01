CIRCA
=====

Generative design code to produce large map-like images. Database-backed for saving and efficient collision detection during generation. 

MySQL, PHP, Javascript, SVG, CSS

In this version the Javascript is used to send AJAX requests for map events and then update the visible SVG in return. Events can cause changes in the data such as new roads, new properties, properties expanding by acquiring neighbouring property, structural growth, destruction or replacement. Each event goes with various parameters such as economic conditions which will affect size and decedance of structure and public or private dominence which will affect variety of building. PHP does all the generation of items on the map server-side with careful collision detection. 

The end goal is to output the SVG as a file which can be loaded into a CNC router to produce a plywood printing block for large scale printing.

References
----------

Scalable Vector Graphics (SVG) 1.1 (Second Edition) http://www.w3.org/TR/SVG/
