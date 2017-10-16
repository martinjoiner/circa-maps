<?php

if( $_SERVER['REQUEST_METHOD'] !== 'POST' ){
    throw new exception('Invalid Method', 405);
}

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$objMapInitCrossRoads = new App\MapInitCrossRoads( $_POST['mapID'] );

$arrResult = $objMapInitCrossRoads->generateCrossRoads();

header('Content-Type: application/json');

echo json_encode($arrResult);
