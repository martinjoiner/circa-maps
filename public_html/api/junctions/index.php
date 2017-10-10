<?php

if( $_SERVER['REQUEST_METHOD'] !== 'GET' ){
    throw new Exception('Invalid Method', 405);
}

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$map = new App\MapComplete( $_GET['mapID'] );

header('Content-Type: application/json');

echo json_encode( $map->junctions() );
