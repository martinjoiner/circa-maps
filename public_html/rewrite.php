<?php

$parts = explode('/', $_GET['path']);

$mapID = intval($parts[0]);

$function = '';
if( count($parts) > 1 ){
    $function = $parts[1];
} 

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$map = new \App\MapComplete($mapID);

if( $function === 'develop' ){
    require 'develop.php';
} else if( $function === 'vr' ){
    require 'vr/vr.php';
} else if( $function === 'svg' ){
    require 'svg/svg.php';
} else {
    require 'view.php';
}
