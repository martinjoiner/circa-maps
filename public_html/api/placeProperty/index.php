<?php

if( $_SERVER['REQUEST_METHOD'] !== 'POST' ){
    throw new exception('Invalid Method', 405);
}

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$objPropertyPlacer = new App\PropertyPlacer( $_POST['mapID'], $_POST['x'], $_POST['y'] );

$point = new App\Point($_POST['x'], $_POST['y']);

$arrResult = $objPropertyPlacer->placeProperty( $point );

header('Content-Type: application/json');

echo json_encode($arrResult);
