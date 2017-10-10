<?php

if( $_SERVER['REQUEST_METHOD'] !== 'GET' ){
    throw new Exception('Invalid Method', 405);
}

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$maps = new App\Maps();

header('Content-Type: application/json');

echo json_encode( $maps->all() );
