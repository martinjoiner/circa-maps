<!DOCTYPE html>
<html>
	<head>
		<title>CoordinateGeometry Tests</title>
	</head>
	<body>

		<?php

		require_once( $_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

		require_once( $_SERVER['DOCUMENT_ROOT'] . '/phDump/phDump.inc.php' );




		print '<h3>Scenario 1</h3>';
		$pointA = new Point( 20, 10 );
		$pointB = new Point( 20, 30 );
		$pointC = new Point( 10, 10 );
		$pointD = new Point( 30, 30 );

		$lineSegmentA = [ $pointA, $pointB ];
		$lineSegmentB = [ $pointC, $pointD ];

		phDump( CoordinateGeometry::lineSegmentIntersectionPoint( $lineSegmentA, $lineSegmentB ) );




		print '<h3>Scenario 2</h3>';
		$pointA = new Point( 10, 10 );
		$pointB = new Point( 10, 30 );
		$pointC = new Point( 20, 10 );
		$pointD = new Point( 40, 30 );

		$lineSegmentA = [ $pointA, $pointB ];
		$lineSegmentB = [ $pointC, $pointD ];

		phDump( CoordinateGeometry::lineSegmentIntersectionPoint( $lineSegmentA, $lineSegmentB ) );




		print '<h3>Scenario 3</h3>';
		$pointA = new Point( 20, 10 );
		$pointB = new Point( 20, 30 );
		$pointC = new Point( 10, 15 );
		$pointD = new Point( 30, 35 );

		$lineSegmentA = [ $pointA, $pointB ];
		$lineSegmentB = [ $pointC, $pointD ];

		phDump( CoordinateGeometry::lineSegmentIntersectionPoint( $lineSegmentA, $lineSegmentB ) );

		?>

	</body>
</html>
