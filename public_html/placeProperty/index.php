<?php

header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

$objMapSection = new MapSection( 1, $_GET['x'], $_GET['y'] );

$arrResult = $objMapSection->placeProperty( $_GET['x'], $_GET['y'] );

// Return result as JSON 
echo json_encode($arrResult);
