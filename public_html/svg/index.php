<?php

if( !array_key_exists('mapID', $_GET) ){
	$_GET['mapID'] = 1;
}

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

$map = new MapComplete($_GET['mapID']);

header('Content-Type: image/svg+xml');
header('Content-Disposition: attachment; filename="' . date("Y-m-d") . ' - ' . $map->name . '.svg"');

echo $map->printFileMarkup();
