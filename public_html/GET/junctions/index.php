<?php

header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

$map = new MapComplete( $_GET['mapID'] );

echo json_encode( $map->junctions() );
