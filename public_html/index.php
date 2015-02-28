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
		include('autoloadRegister.inc.php');
		$svg = new Svg(1);
		print $svg->printMarkup(false);
		?>
		<div id="mask"></div>
	</div>

	<div id="svgJson"><?=json_encode( $svg->arrPaths )?></div>

	<form>
		<input type="text" id="mouseCoord" value="">
		<fieldset>
			<legend>Mouse Mode</legend>
			<input type="radio" name="mouseMode" value="IsOccupied" checked> IsOccupied<br>
			<input type="radio" name="mouseMode" value="redDot"> RedDot
		</fieldset>
		<input type="button" id="btnSpawn" value="Spawn">
		<input type="button" id="btnStop" value="Stop">
	</form>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>
	<script src="/js/circa.js"></script>

</body>
</html>
