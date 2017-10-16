
# Circa

Generative design project to produce large map-like artworks. Events will be written to assess conditions and trigger changes in the size, shape and placement of roads and properties to mimick the expansion of a real human-populated city. Parameters such as economic conditions will affect size and decedance of structures built on a property. Political factors such as public or private-sector dominance will affect variety of building. Land value will be determined by proximity to major routes and value of neighbouring properties.


![Photographed segment of a print by Ashley Thomas](/docs/Segment-of-AT-Print.jpg)

The above image is a section of a print by Ashley Thomas. The inspiration for this project is to explore the question of what a code version of Ashley's very slow manual process could look like? 


![Screenshot of Circa at end of 2015](/docs/screenshot-2015.jpg)

Above: Image shows the level of complexity that the project was able to generate at end of 2015


![Example from February 2016](/docs/2016-02-20-Fourth-Map.jpg)

Above: Example from February 2016

Live at: http://circa.butterscotchworld.co.uk




## Target Goal

The initial intention for this project was to be able to automatically generate highly detailed SVG files containing millions of properties which could then be loaded into a CNC router to cut a huge plywood printing block (at least 6 ft). This would be inked and pressed using a ride-on road roller at a public event open to spectators similar to this video from a Steamroller Festival in Canada: https://youtu.be/1tHgtmHc0bI 

However, in December 2016, after receiving estimates of the cost and cutting time for such a large and complex pattern it soon became apparent this was unviable without significant financial input. 

In October 2017 the project was reborn as a VR environment which can be experienced through a headset. 




## Interface routes

The application offers several ways to interact with the data:

### View

`/{id}` - A non-interactive web-page for viewing an SVG of a map.

### Develop

`/{id}/develop` - An interactive SVG view of the map with control panel to to send API requests to trigger map events and return results, updating the map if necessery. 

### SVG

`/{id}/svg` - Download an SVG file of the map

### API Routes

`/api/...` - Many operations can be triggered in isolation for development purposes. 




## Real-world events to be simulated

 - 2 small neighbouring properties on high value land might merge to enable them to build taller. 
 - Wars or natural disasters may raze structures. 




## Technology 

MySQL, PHP 7, JavaScript, jQuery, SVG, CSS, WebGL.

PHP does all the generation of items on the map, collision detection and reading/writing the database. 

MySQL database-backed for saving data and efficiently being able to perform operations on a small section of the map by selecting points with a range. 




## References

Scalable Vector Graphics (SVG) 1.1 (Second Edition) http://www.w3.org/TR/SVG/

Coordinate Geometry http://www.mathopenref.com/coordintersection.html 
