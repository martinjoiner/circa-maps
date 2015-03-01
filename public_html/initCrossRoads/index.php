<?php

header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

// Initialise a MapSection object
$objMapInitCrossRoads = new MapInitCrossRoads( 1 );

// Check if point is occupied
$arrResult = $objMapInitCrossRoads->generateCrossRoads();

// Return result as JSON 
echo json_encode($arrResult);
