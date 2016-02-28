<!DOCTYPE html>
<html>
	<head>
		<title>PropertyRelativePoint Tests</title>
	</head>
	<body>

		<?php		

		include($_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

		// Square Property
		$arrPoints[] = new Point( 10, 10 );
		$arrPoints[] = new Point( 60, 10 );
		$arrPoints[] = new Point( 60, 60 );
		$arrPoints[] = new Point( 10, 60 );
		$squareProperty = new Property( $arrPoints, 5);


		// Create an array of points on the square Property to be translated onto all properties
		$arrConvertPoints = [];
		$arrConvertPoints[] = new Point( 22, 35 );
		$arrConvertPoints[] = new Point( 28, 16 );
		$arrConvertPoints[] = new Point( 50, 50 );
		$arrConvertPoints[] = new Point( 50, 18 );
		$arrConvertPoints[] = new Point( 35, 35 );


		// Wonky Property
		$arrPoints[0] = new Point( 100, 100 );
		$arrPoints[1] = new Point( 150, 150 );
		$arrPoints[2] = new Point( 100, 200 );
		$arrPoints[3] = new Point( 60, 140 );
		$wonkyProperty = new Property( $arrPoints, 5);


		// Long Property
		$arrPoints[0] = new Point( 200, 10 );
		$arrPoints[1] = new Point( 400, 30 );
		$arrPoints[2] = new Point( 400, 100 );
		$arrPoints[3] = new Point( 200, 90 );
		$longProperty = new Property( $arrPoints, 5);


		// Tall Property
		$arrPoints[0] = new Point( 200, 120 );
		$arrPoints[1] = new Point( 247, 130 );
		$arrPoints[2] = new Point( 244, 289 );
		$arrPoints[3] = new Point( 192, 300 );
		$tallProperty = new Property( $arrPoints, 5);


		// Tiny Property
		$arrPoints[0] = new Point( 300, 120 );
		$arrPoints[1] = new Point( 310, 130 );
		$arrPoints[2] = new Point( 300, 140 );
		$arrPoints[3] = new Point( 290, 130 );
		$tinyProperty = new Property( $arrPoints, 5);


		// Array of PropertyRelativePoints
		for( $i = 0; $i < count($arrConvertPoints); $i++ ){
			$arrPropertyRelativePoints[$i] = new PropertyRelativePoint( $squareProperty, $arrConvertPoints[$i] );
			$arrWonkyPropertyRelativePoints[$i] = $arrPropertyRelativePoints[$i]->absolutePoint( $wonkyProperty );
			$arrLongPropertyRelativePoints[$i] = $arrPropertyRelativePoints[$i]->absolutePoint( $longProperty );
			$arrTallPropertyRelativePoints[$i] = $arrPropertyRelativePoints[$i]->absolutePoint( $tallProperty );
			$arrTinyPropertyRelativePoints[$i] = $arrPropertyRelativePoints[$i]->absolutePoint( $tinyProperty );
		}

		?>

		<svg id="svgMap" xmlns="http://www.w3.org/2000/svg" width="1200" height="800" viewBox="0 0 1200 800" enable-background="new 0 0 1200 800">
			<style type="text/css"><![CDATA[
				.Route, .Property{ stroke: #555; stroke-opacity: 1;  }
			    .Route{ fill: none; stroke-linejoin: round; }
			    .Property{ fill: #777; opacity: 0.5; stroke-width: 0; }
			    .Front{ stroke: #E22; }
			    .DebugPath{ fill-opacity: 0.5; }
			]]></style>

			<?=$squareProperty->printMarkup()?>

			<?=$wonkyProperty->printMarkup()?>

			<?=$longProperty->printMarkup()?>

			<?=$tallProperty->printMarkup()?>

			<?=$tinyProperty->printMarkup()?>
			
			<?php

			for( $i = 0; $i < count($arrConvertPoints); $i++ ){
				switch($i){
					case 0: $color = 'red'; break;
					case 1: $color = 'blue'; break;
					case 2: $color = 'green'; break;
					case 3: $color = 'yellow'; break;
					case 4: $color = 'purple'; break;
				}
				?>
				<circle cx="<?=$arrConvertPoints[$i]->x?>" cy="<?=$arrConvertPoints[$i]->y?>" r="2" fill="<?=$color?>" class="Dot"></circle>
				<circle cx="<?=$arrWonkyPropertyRelativePoints[$i]->x?>" cy="<?=$arrWonkyPropertyRelativePoints[$i]->y?>" r="2" fill="<?=$color?>" class="Dot"></circle>
				<circle cx="<?=$arrLongPropertyRelativePoints[$i]->x?>" cy="<?=$arrLongPropertyRelativePoints[$i]->y?>" r="2" fill="<?=$color?>" class="Dot"></circle>
				<circle cx="<?=$arrTallPropertyRelativePoints[$i]->x?>" cy="<?=$arrTallPropertyRelativePoints[$i]->y?>" r="2" fill="<?=$color?>" class="Dot"></circle>
				<circle cx="<?=$arrTinyPropertyRelativePoints[$i]->x?>" cy="<?=$arrTinyPropertyRelativePoints[$i]->y?>" r="2" fill="<?=$color?>" class="Dot"></circle>
				<?php
			}
			?>
			
		</svg>

	</body>
</html>
