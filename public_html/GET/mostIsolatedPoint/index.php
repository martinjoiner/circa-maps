<?php

header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

$objMapSection = new MapMostIsolated( $_GET['mapID'] );

$arrResult = $objMapSection->findMostIsolatedPoint();

echo json_encode($arrResult);
