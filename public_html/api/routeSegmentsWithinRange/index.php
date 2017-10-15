<?php

if( $_SERVER['REQUEST_METHOD'] !== 'GET' ){
    throw new exception('Invalid Method', 405);
}

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$mapSection = new App\MapSection( $_GET['mapID'], $_GET['x'], $_GET['y'] );

$point = new App\Point( $_GET['x'], $_GET['y'] );

$arrResult = $mapSection->getRouteSegmentsWithinRange( $point );

header('Content-Type: application/json');

echo json_encode($arrResult);
