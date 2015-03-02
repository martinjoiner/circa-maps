<?php

header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

$objPropertyPlacer = new PropertyPlacer( $_GET['mapID'], $_GET['x'], $_GET['y'] );

$arrResult = $objPropertyPlacer->placeProperty( $_GET['x'], $_GET['y'] );

echo json_encode($arrResult);
