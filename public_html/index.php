<!DOCTYPE html>
<html>
<head>
	<title>Circa</title>
	<meta charset="utf-8">

	<link rel="stylesheet" type="text/css" href="/css/circa.css">

</head>
<body>

	<div class="canvasWrap">
		<?php

		if( !array_key_exists('mapID', $_GET) ){
			$_GET['mapID'] = 1;
		}

		include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');
		$objMap = new MapComplete($_GET['mapID']);
		echo $objMap->printMarkup();
		?>
		<div id="mask"></div>
	</div>


	<form class="controlPanel">

		<input type="hidden" name="mapID" id="mapID" value="<?=$_GET['mapID']?>">

		<label>Mouse co-ordinates</label>
		<input type="text" id="mouseCoord" value="">

		<fieldset class="mouseMode">
			<legend>Mouse Mode</legend>
			<input type="radio" name="mouseMode" value="isOccupied" id="chkIsOccupied" checked><label for="chkIsOccupied" class="checkLabel">&quot;Is occupied?&quot; query</label><br>
			<input type="radio" name="mouseMode" value="redDot" id="chkRedDot"><label for="chkRedDot" class="checkLabel">Red dot</label><br>
			<input type="radio" name="mouseMode" value="nearestRoute" id="chkNearestRoute"><label for="chkNearestRoute" class="checkLabel">Nearest route</label><br>
			<input type="radio" name="mouseMode" value="placeProperty" id="chkPlaceProperty"><label for="chkPlaceProperty" class="checkLabel">Place property</label><br>
			<input type="radio" name="mouseMode" value="deleteProperty" id="chkDeleteProperty"><label for="chkDeleteProperty" class="checkLabel">Delete property</label><br>
			<input type="radio" name="mouseMode" value="offsetPoints" id="chkOffsetPoints"><label for="chkOffsetPoints" class="checkLabel">Offset Points</label>
		</fieldset>

		<p id="spawnNotify">Spawning</p>
		<input type="button" id="btnSpawnStart" value="Spawn">
		<input type="button" id="btnSpawnStop" value="Stop spawning">
		
		
		<table>
			<tbody>
				<tr>
					<th></th>
					<td colspan="2"><input type="button" id="btnInitXRoads" value="Init X-roads"></td>
					<td>&nbsp;</td>
				</tr>
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
			</tbody>
		</table>

		<a href="?mapID=1">Map 1</a> <a href="?mapID=2">Map 2</a> <a href="?mapID=3">Map 3</a> 

	</form>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>
	<script src="/js/circa.js"></script>

</body>
</html>
