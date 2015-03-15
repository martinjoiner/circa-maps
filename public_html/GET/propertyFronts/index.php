<?php

header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

$objMap = new MapComplete( $_GET['mapID'] );

$arrPropertyFronts = $objMap->getPropertyFronts();

echo json_encode($arrPropertyFronts);
