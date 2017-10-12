<?php

if( $_SERVER['REQUEST_METHOD'] !== 'GET' ){
    throw new exception('Invalid Method', 405);
}

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$map = new App\MapComplete( $_GET['mapID'] );

if( isSet($_GET['format']) ){
    $properties = $map->getProperties( $_GET['format'] );
} else {
    $properties = $map->getProperties();
}

header('Content-Type: application/json');

echo json_encode($properties);
