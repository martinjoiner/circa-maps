<?php

header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

$objMapSection = new MapSection( $_GET['mapID'], $_GET['x'], $_GET['y'] );

$arrResult = $objMapSection->improvePropertyAtPoint( $_GET['x'], $_GET['y'] );

echo json_encode($arrResult);
