<?php

if( $_SERVER['REQUEST_METHOD'] !== 'POST' ){
    throw new exception('Invalid Method', 405);
}

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$propertyDeleter = new App\PropertyDeleter( $_POST['mapID'], $_POST['x'], $_POST['y'], 30, 30 );

header('Content-Type: application/json');

echo json_encode( $propertyDeleter->cleanseAll() );
