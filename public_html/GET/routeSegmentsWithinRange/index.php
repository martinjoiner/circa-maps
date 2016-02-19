<?php

header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

$objMap = new MapSection( $_GET['mapID'], $_GET['x'], $_GET['y'] );

$point = new Point( $_GET['x'], $_GET['y'] );

$arrResult = $objMap->getRouteSegmentsWithinRange( $point );

echo json_encode($arrResult);
