<?php

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

// Initialise a MapSection object
$objMapSection = new MapSection( 1, $_GET['x'], $_GET['y'] );

// Check if point is occupied
$arrResult = $objMapSection->isOccupied( $_GET['x'], $_GET['y'] );

// Return result as JSON 
echo json_encode($arrResult);
