<?php

header('Content-Type: image/svg+xml');
header('Content-Disposition: attachment; filename="' . date("Y-m-d") . ' - ' . $map->name . '.svg"');

echo $map->printFileMarkup();
