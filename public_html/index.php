<!DOCTYPE html>
<html>
<head>

	<title>Circa Maps - A generative design project by Martin Joiner</title>

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

			<input type="radio" name="mouseMode" value="isOccupied" id="chkIsOccupied" checked>
			<label for="chkIsOccupied" class="checkLabel">&quot;Is occupied?&quot; query</label>

			<input type="radio" name="mouseMode" value="redDot" id="chkRedDot">
			<label for="chkRedDot" class="checkLabel">Red dot</label>

			<input type="radio" name="mouseMode" value="nearestRoute" id="chkNearestRoute">
			<label for="chkNearestRoute" class="checkLabel">Nearest route</label>

			<input type="radio" name="mouseMode" value="placeProperty" id="chkPlaceProperty">
			<label for="chkPlaceProperty" class="checkLabel">Place property</label>

			<input type="radio" name="mouseMode" value="deleteProperty" id="chkDeleteProperty">
			<label for="chkDeleteProperty" class="checkLabel">Delete property</label>

			<input type="radio" name="mouseMode" value="offsetSides" id="chkOffsetSides">
			<label for="chkOffsetSides" class="checkLabel">Offset Sides</label>

			<input type="radio" name="mouseMode" value="improvePropertyAtPoint" id="chkImprovePropertyAtPoint">
			<label for="chkImprovePropertyAtPoint" class="checkLabel">Improve property</label>
			
		</fieldset>


		<fieldset class="actionsField">
			<legend>Actions</legend>

			<button id="btnSpawnStartStop">
				<i></i>
				<span>Start Spawning</span>
			</button>
			
			<input type="button" id="btnInitXRoads" value="Init X-roads">

			<input type="button" id="btnMostIsolated" value="Most isolated point">

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
				</tbody>
			</table>

		</fieldset>


		<fieldset>
			<legend>Maps</legend>
			<ul>
				<?php
				for( $i=1; $i < 5; $i++ ){
					?>
					<li><a href="?mapID=<?=$i?>">Map <?=$i?></a></li>
					<?php
				}
				?>
			</ul>
		</fieldset>

	</form>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>
	<script src="/js/circa.js"></script>

</body>
</html>
