<?php 

//header('Content-Type: image/svg+xml');

echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'; 

include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');
include($_SERVER['DOCUMENT_ROOT'] . '/phDump/phDump.inc.php');

$objMap = new MapComplete(1);

echo $objMap->printMarkup();
