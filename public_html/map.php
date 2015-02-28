<?php 

//header('Content-Type: image/svg+xml');

echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'; 

include('autoloadRegister.inc.php');

$objMap = new MapComplete(1);

echo $objMap->printMarkup();
