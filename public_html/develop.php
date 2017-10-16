<!DOCTYPE html>
<html>
<head>

    <title>Circa Maps - A generative design project by Martin Joiner</title>

    <link rel="stylesheet" type="text/css" href="/css/circa.css">

</head>
<body>

    <div class="canvasWrap" style="width: <?=$map->getWidth()?>px; height: <?=$map->getHeight()?>px">
        <?=$map->printMarkup();?>
        <div id="mask"></div>
    </div>


    <form class="controlPanel">

        <input type="hidden" name="mapID" id="mapID" value="<?=$mapID?>">

        <label>Mouse co-ordinates</label>
        <input type="text" id="mouseCoord" value="">

        <fieldset class="mouseMode">
            <legend>Mouse Mode</legend>

            <input type="radio" name="mouseMode" value="isOccupied" id="chkIsOccupied" checked>
            <label for="chkIsOccupied" class="checkLabel">&quot;Is occupied?&quot; query</label>

            <input type="radio" name="mouseMode" value="redDot" id="chkRedDot">
            <label for="chkRedDot" class="checkLabel">Red dot</label>

            <input type="radio" name="mouseMode" value="marker1" id="chkMarker1">
            <label for="chkMarker1" class="checkLabel">Place Marker 1</label>

            <input type="radio" name="mouseMode" value="marker2" id="chkMarker2">
            <label for="chkMarker2" class="checkLabel">Place Marker 2</label>

            <input type="radio" name="mouseMode" value="nearestRoute" id="chkNearestRoute">
            <label for="chkNearestRoute" class="checkLabel">Nearest route</label>

            <input type="radio" name="mouseMode" value="placeProperty" id="chkPlaceProperty">
            <label for="chkPlaceProperty" class="checkLabel">Place property</label>

            <input type="radio" name="mouseMode" value="deleteProperty" id="chkDeleteProperty">
            <label for="chkDeleteProperty" class="checkLabel">Delete property</label>

            <input type="radio" name="mouseMode" value="cleanseArea" id="chkCleanseArea">
            <label for="chkCleanseArea" class="checkLabel">Cleanse area</label>

            <input type="radio" name="mouseMode" value="offsetSides" id="chkOffsetSides">
            <label for="chkOffsetSides" class="checkLabel">Offset Sides</label>

            <input type="radio" name="mouseMode" value="improvePropertyAtPoint" id="chkImprovePropertyAtPoint">
            <label for="chkImprovePropertyAtPoint" class="checkLabel">Improve property</label>

            <input type="radio" name="mouseMode" value="routeSegmentsWithinRange" id="chkRouteSegmentsWithinRange">
            <label for="chkRouteSegmentsWithinRange" class="checkLabel">Route segments</label>
            
        </fieldset>


        <fieldset class="actionsField">
            <legend>Actions</legend>

            <button id="btnSpawnStartStop">
                <i></i>
                <span>Start Spawning</span>
            </button>
            
            <input type="button" id="btnInitXRoads" value="Init X-roads" <?php if( $map->getRouteCount() ){ print 'disabled'; } ?>>

            <input type="button" id="btnMostIsolated" value="Most isolated point">

            <input type="button" id="btnShortestTravel" value="Shortest Travel">

        </fieldset>
        

        <fieldset class="renderField">
            <legend>Render</legend>

            <table>
                <tbody>
                    <tr>
                        <th>Routes</th>
                        <td><input type="button" id="btnDrawRoutes" value="Draw"></td>
                        <td><input type="button" id="btnDeleteRoutes" value="Delete"></td>
                    </tr>
                    <tr>
                        <th>Properties</th>
                        <td><input type="button" id="btnDrawProperties" value="Draw"></td>
                        <td><input type="button" id="btnDeleteProperties" value="Delete"></td>
                    </tr>
                    <tr>
                        <th>Fronts</th>
                        <td><input type="button" id="btnDrawFronts" value="Draw"></td>
                        <td><input type="button" id="btnDeleteFronts" value="Delete"></td>
                    </tr>
                    <tr>
                        <th>Junctions</th>
                        <td><input type="button" id="btnDrawJunctions" value="Draw"></td>
                        <td><input type="button" id="btnDeleteJunctions" value="Delete"></td>
                    </tr>
                </tbody>
            </table>

        </fieldset>


        <fieldset>
            <legend>Maps</legend>
            <ul>
                <?php
                $maps = new App\Maps();
                $maps = $maps->all();
                foreach( $maps as $map ){
                    ?>
                    <li><a href="/<?=$map['id']?>/develop"><?=$map['id']?>: <?=$map['name']?></a></li>
                    <?php
                }
                ?>
            </ul>
        </fieldset>

        <p><a href="/<?=$mapID?>/svg">Download SVG file</a></p>

    </form>

    <script src="http://code.jquery.com/jquery-2.2.4.min.js"
            integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
            crossorigin="anonymous"></script>
    <script src="/js/circa.js"></script>

</body>
</html>
