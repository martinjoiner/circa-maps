<?php

if( $_SERVER['REQUEST_METHOD'] !== 'GET' ){
    throw new Exception('Invalid Method', 405);
}

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$pointA = new App\Point($_GET['x0'], $_GET['y0']);
$pointB = new App\Point($_GET['x1'], $_GET['y1']);

$map = new App\TravelMap( $_GET['mapID'] );

$result = $map->travelShortest( $pointA, $pointB );

header('Content-Type: application/json');

echo json_encode($result);
