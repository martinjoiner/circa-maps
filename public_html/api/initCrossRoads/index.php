<?php

if( $_SERVER['REQUEST_METHOD'] !== 'POST' ){
    throw new exception('Invalid Method', 405);
}

header('Content-Type: application/json');

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

// Initialise a MapSection object
$objMapInitCrossRoads = new App\MapInitCrossRoads( $_POST['mapID'] );

// Check if point is occupied
$arrResult = $objMapInitCrossRoads->generateCrossRoads();

// Return result as JSON 
echo json_encode($arrResult);
