<?php 
header('Content-Type: image/svg+xml');
echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'; 

include('autoloadRegister.inc.php');

$svg = new Svg();
print $svg->printMarkup();
/*
print '<p>' . $svg->getAAP() . '</p>';

print $svg->isOccupied('300,50');
print $svg->isOccupied('300,100');
print $svg->isOccupied('300,150');
print $svg->isOccupied('300,200');
print $svg->isOccupied('300,250');
print $svg->isOccupied('300,300');
print $svg->isOccupied('300,350');
print $svg->isOccupied('300,400');
print $svg->isOccupied('300,450');
print $svg->isOccupied('300,500');
print $svg->isOccupied('300,550');
print $svg->isOccupied('300,600');
print $svg->isOccupied('300,650');
print $svg->isOccupied('300,700');

print $svg->isOccupied('400,400');
print $svg->isOccupied('400,450');
print $svg->isOccupied('400,500');
print $svg->isOccupied('400,550');
print $svg->isOccupied('400,600');
print $svg->isOccupied('400,650');
print $svg->isOccupied('400,700');

print $svg->isOccupied('500,400');
print $svg->isOccupied('500,450');
print $svg->isOccupied('500,500');
print $svg->isOccupied('500,550');
print $svg->isOccupied('500,600');
print $svg->isOccupied('500,650');
print $svg->isOccupied('500,700');

print $svg->isOccupied('600,400');
print $svg->isOccupied('600,450');
print $svg->isOccupied('600,550');
print $svg->isOccupied('600,550');
print $svg->isOccupied('600,600');
print $svg->isOccupied('600,650');
print $svg->isOccupied('600,700');
*/
?>