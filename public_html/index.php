<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

$maps = new App\Maps();
$maps = $maps->all();

?><!DOCTYPE html>
<html>
    <head>
        <title>Circa by Martin Joiner</title>
    </head>
    <body>

        <h1>Circa</h1>

        <?php
        foreach( $maps as $map ){
            ?>
            <li><a href="<?=$map['id']?>"><?=$map['name']?></a></li>
            <?php
        }
        ?>    

    </body>
</html>
