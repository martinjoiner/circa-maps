<!DOCTYPE html>
<html>
<head>

    <title>Circa Maps - A generative design project by Martin Joiner</title>

    <link rel="stylesheet" type="text/css" href="/css/circa.css">

</head>
<body>

    <div class="canvasWrap" style="width: <?=$map->getWidth()?>px; height: <?=$map->getHeight()?>px">
        <?=$map->printMarkup();?>
    </div>
    
    <p><a href="/<?=$map->getId()?>/vr">VR</a></p>

</body>
</html>
