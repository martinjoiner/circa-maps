<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\CoordinateGeometry;
use App\Point;

/**
 * @covers CoodinateGeometry
 */
final class CoodinateGeometryTest extends TestCase
{

    /**
     * A line
     */
    public function testEquationOfLine()
    {
        $pointA = new Point( 10, 10 );
        $pointB = new Point( 20, 15 );
        $equationOfLine = CoordinateGeometry::equationOfLine( $pointA, $pointB ); 

        $expected = [
            "m" => 0.49999999999999989,
            "b" => 5.0,
            "x" => null,
            "equation" => "y = 0.5x + 5",
            "isVertical" => false,
            "isHorizontal" => false
        ];

        $this->assertEquals($expected,$equationOfLine);
    }

    /**
     * A perfect 45 degree diagonal line
     */
    public function testEquationOfDiagonalLine()
    {
        $pointA = new Point( 10, 10 );
        $pointB = new Point( 20, 20 );
        $equationOfLine = CoordinateGeometry::equationOfLine( $pointA, $pointB ); 

        $expected = [
            "m" => 1.0,
            "b" => 0.0,
            "x" => null,
            "equation" => "y = 1x + 0",
            "isVertical" => false,
            "isHorizontal" => false
        ];

        $this->assertEquals($expected,$equationOfLine);
    }

    /**
     * A horizontal line
     */
    public function testEquationOfHorizontalLine()
    {
        $pointA = new Point( 10, 10 );
        $pointB = new Point( 20, 10 );
        $equationOfLine = CoordinateGeometry::equationOfLine( $pointA, $pointB ); 

        $expected = [
            "m" => 0,
            "b" => 10,
            "x" => null,
            "equation" => "y = 10",
            "isVertical" => false,
            "isHorizontal" => true
        ];

        $this->assertEquals($expected,$equationOfLine);
    }

    /**
     * A vertical line
     */
    public function testEquationOfVerticalLine()
    {
        $pointA = new Point( 10, 10 );
        $pointB = new Point( 10, 55 );
        $equationOfLine = CoordinateGeometry::equationOfLine( $pointA, $pointB ); 

        $expected = [
            "m" => null,
            "b" => null,
            "x" => 10,
            "equation" => "x = 10",
            "isVertical" => true,
            "isHorizontal" => false
        ];

        $this->assertEquals($expected,$equationOfLine);
    }

    /**
     * A line with point A on the right of point B!
     */
    public function testEquationOfBackwardsLine()
    {
        $pointA = new Point( 20, 20 );
        $pointB = new Point( 10, 10 );
        $equationOfLine = CoordinateGeometry::equationOfLine( $pointA, $pointB ); 

        $expected = [
            "m" => 0.99999999999999978,
            "b" => 0.0,
            "x" => null,
            "equation" => "y = 1x + 0",
            "isVertical" => false,
            "isHorizontal" => false
        ];

        $this->assertEquals($expected,$equationOfLine);
    }

}
