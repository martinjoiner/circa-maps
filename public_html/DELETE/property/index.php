<?php

header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

$objPropertyDeleter = new PropertyDeleter( $_GET['mapID'], $_GET['x'], $_GET['y'] );

$point = new Point( $_GET['x'], $_GET['y'] );

$arrResult = $objPropertyDeleter->deleteProperties( $point );

echo json_encode($arrResult);
