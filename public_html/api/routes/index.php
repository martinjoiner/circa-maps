<?php

if( $_SERVER['REQUEST_METHOD'] !== 'GET' ){
    throw new exception('Invalid Method', 405);
}

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$map = new App\MapComplete( $_GET['mapID'] );

$arrRoutes = $map->getRoutes();

header('Content-Type: application/json');

echo json_encode($arrRoutes);
