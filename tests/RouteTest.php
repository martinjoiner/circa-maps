<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Point;
use App\Route;

/**
 * @covers Route
 */
final class RouteTest extends TestCase
{

    public function testGetLength()
    {
        $points = [
            new Point( 10, 10 ),
            new Point( 20, 20 ),
            new Point( 30, 20 )
        ];
        $route = new Route(1, $points); 

        $this->assertEquals($route->getLength(), 24.1);
    }

}
