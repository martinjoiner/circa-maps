<?php

if( $_SERVER['REQUEST_METHOD'] !== 'GET' ){
    throw new Exception('Invalid Method', 405);
}

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$objMapSection = new App\MapSection( $_GET['mapID'], $_GET['x'], $_GET['y'] );

$arrResult = $objMapSection->isOccupied( $_GET['x'], $_GET['y'] );

header('Content-Type: application/json');

echo json_encode($arrResult);
