<?php

header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

$objMap = new MapComplete(1);

$arrPropertyFronts = $objMap->getPropertyFronts();

echo json_encode($arrPropertyFronts);
