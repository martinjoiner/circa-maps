<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';


use App\Property;
use App\Point;
use App\Path;
use App\Structure;


// Define base property, a nice horizontal rectangle
$baseProperty = new Property([
    new Point(10,10),
    new Point(180,10),
    new Point(180,150),
    new Point(10,150),
]);


$structure = new Structure([
    new Path([
        new Point(15,35),
        new Point(40,35),
        new Point(40,20),
        new Point(15,20),
    ]),
    new Path([
        new Point(15,40),
        new Point(30,40),
        new Point(30,45),
        new Point(15,45),
    ]),
    new Path([
        new Point(65,40),
        new Point(70,40),
        new Point(70,45),
        new Point(65,45),
    ]),
    new Path([
        new Point(150,20),
        new Point(170,20),
        new Point(170,140),
        new Point(150,140),
    ]),
], $baseProperty);



// Define destination properties in a variety of wonky shapes at a funny angles

$destinationPropertyA = new Property([
    new Point(210,10),
    new Point(280,10),
    new Point(280,50),
    new Point(210,50),
]);

$destinationPropertyB = new Property([
    new Point(130,170),
    new Point(180,200),
    new Point(140,230),
    new Point(110,190),
]);

$destinationPropertyC = new Property([
    new Point(330,90),
    new Point(390,140),
    new Point(390,180),
    new Point(310,180),
]);

$destinationPropertyD = new Property([
    new Point(10,390),
    new Point(390,390),
    new Point(390,190),
    new Point(310,190),
]);

// Really wide, not very tall
$destinationPropertyE = new Property([
    new Point(300,10),
    new Point(790,10),
    new Point(790,40),
    new Point(300,40),
]);

$destinationPropertyF = new Property([
    new Point(540,230),
    new Point(510,140),
    new Point(530,130),
    new Point(580,200),
]);




header('Content-Type: image/svg+xml');
print "<!DOCTYPE svg PUBLIC \"-//W3C//DTD SVG 1.1//EN\" \"http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd\">\n";
print "<svg id=\"svgMap\" xmlns=\"http://www.w3.org/2000/svg\" width=\"800\" height=\"600\" viewBox=\"0 0 800 600\" enable-background=\"new 0 0 800 600\">\n";

print "<style type=\"text/css\"><![CDATA[
					.Property{ stroke: #555; stroke-opacity: 1; fill: #777; opacity: 0.5; stroke-width: 0; }
				  ]]></style>\n";

print $baseProperty->printMarkup();
print $destinationPropertyA->printMarkup();
print $destinationPropertyB->printMarkup();
print $destinationPropertyC->printMarkup();
print $destinationPropertyD->printMarkup();
print $destinationPropertyE->printMarkup();
print $destinationPropertyF->printMarkup();


print $structure->markup();
print $structure->markupOnProperty($destinationPropertyA);
print $structure->markupOnProperty($destinationPropertyB);
print $structure->markupOnProperty($destinationPropertyC);
print $structure->markupOnProperty($destinationPropertyD);
print $structure->markupOnProperty($destinationPropertyE);
print $structure->markupOnProperty($destinationPropertyF);

print "</svg>\n";
