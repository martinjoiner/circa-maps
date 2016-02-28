<!DOCTYPE html>
<html>
	<head>
		<title>CoordinateGeometry Tests</title>
	</head>
	<body>

		<?php

		require_once( $_SERVER['DOCUMENT_ROOT'] . '/autoloadRegister.inc.php');

		require_once( $_SERVER['DOCUMENT_ROOT'] . '/phDump/phDump.inc.php' );


		print '<h3>A diagonal line</h3>';
		$pointA = new Point( 10, 10 );
		$pointB = new Point( 20, 20 );
		phDump( CoordinateGeometry::equationOfLine( $pointA, $pointB ) ); 


		print '<h3>A line</h3>';
		$pointA = new Point( 10, 10 );
		$pointB = new Point( 20, 15 );
		phDump( CoordinateGeometry::equationOfLine( $pointA, $pointB ) );


		print '<h3>A vertical line</h3>';
		$pointA = new Point( 10, 10 );
		$pointB = new Point( 10, 55 );
		phDump( CoordinateGeometry::equationOfLine( $pointA, $pointB ) );


		print '<h3>A line with point A on the right of point B!</h3>';
		$pointA = new Point( 20, 20 );
		$pointB = new Point( 10, 10 );
		phDump( CoordinateGeometry::equationOfLine( $pointA, $pointB ) );



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
