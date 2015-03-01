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
		include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');
		$objMap = new MapComplete(1);
		echo $objMap->printMarkup();
		?>
		<div id="mask"></div>
	</div>


	<form class="controlPanel">
		<label>Mouse co-ordinates</label>
		<input type="text" id="mouseCoord" value="">

		<fieldset class="mouseMode">
			<legend>Mouse Mode</legend>
			<input type="radio" name="mouseMode" value="isOccupied" id="chkIsOccupied" checked><label for="chkIsOccupied" class="checkLabel">&quot;Is occupied?&quot; query</label><br>
			<input type="radio" name="mouseMode" value="redDot" id="chkRedDot"><label for="chkRedDot" class="checkLabel">Red dot</label><br>
			<input type="radio" name="mouseMode" value="placeProperty" id="chkPlaceProperty"><label for="chkPlaceProperty" class="checkLabel">Place property</label><br>
			<input type="radio" name="mouseMode" value="dropBomb" id="chkDropBomb"><label for="chkDropBomb" class="checkLabel">Drop a bomb</label>
		</fieldset>
		<input type="button" id="btnSpawn" value="Spawn">
		<input type="button" id="btnStop" value="Stop spawning">
		
		
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
			</tbody>
		</table>

	</form>


	<div id="svgJson"><?=json_encode( $svg->arrPaths )?></div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>
	<script src="/js/circa.js"></script>

</body>
</html>
