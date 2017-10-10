<?php

if( $_SERVER['REQUEST_METHOD'] !== 'POST' ){
    throw new exception('Invalid Method', 405);
}

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$propertyDeleter = new PropertyDeleter( $_GET['mapID'], $_GET['x'], $_GET['y'] );

$point = new Point( $_GET['x'], $_GET['y'] );

$arrResult = $propertyDeleter->deleteProperties( $point );

header('Content-Type: application/json');

echo json_encode($arrResult);
