<?php

if( $_SERVER['REQUEST_METHOD'] !== 'GET' ){
    throw new Exception('Invalid Method', 405);
}

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$mapSection = new App\MapMostIsolated( $_GET['mapID'] );

$arrResult = $mapSection->findMostIsolatedPoint();

header('Content-Type: application/json');

echo json_encode($arrResult);
