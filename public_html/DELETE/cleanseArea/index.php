<?php

header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

$propertyDeleter = new PropertyDeleter( $_GET['mapID'], $_GET['x'], $_GET['y'], 30, 30 );

echo json_encode( $propertyDeleter->cleanseAll() );
