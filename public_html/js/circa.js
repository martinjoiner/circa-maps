
var globals = {};
globals.mapID = $('input#mapID').val();

var arrPaths = [];
var count = 0;
var mapWidth 


var isPointOnYou = (function( point ){
	var coordParts = point.split(',');
	var x = parseFloat(coordParts[0]);
	var y = parseFloat(coordParts[1]);
	if( x < 0 || y < 0 || x > this.width || y > this.height ){
		return false;
	}
	return true;
});


var Map = (function(){
	this.width = 1200;
	this.height = 800;

	this.isPointOnYou = isPointOnYou;
});

var map = new Map();


/* Returns 4 points that represent a polygon adjascent to one of the vertex */
/* @sideNum *Optional* which vertex it should be adjascent to 				*/
var spawnOffsetSide = (function( sideNum ){
	if(typeof sideNum === 'undefined'){
		sideNum = 1;
	}

	var arrCoords = [];
	var pos = sideNum - 2;
	for (i = 0; i < 4; i++){
		if( pos < 0 ){
			pos = pos + this.arrAbsolutePoints.length;
		} else if ( pos > this.arrAbsolutePoints.length - 1 ){
			pos = 0;
		}
		arrCoords[i] = pos++;
	}

	var firstRoot = this.arrAbsolutePoints[arrCoords[0]]
	var firstPoint = this.arrAbsolutePoints[arrCoords[1]];
	var secondPoint = this.arrAbsolutePoints[arrCoords[2]];
	var secondRoot = this.arrAbsolutePoints[arrCoords[3]];
	
	// Put simply, this says if looking at side 4 the second point is actually the first point
	if( sideNum > this.arrAbsolutePoints.length - 1 ){
		secondPoint = this.arrAbsolutePoints[0];
	}

	var arrAbsolutePoint = [];
	arrAbsolutePoint[0] = projectPath( secondRoot, secondPoint );
	arrAbsolutePoint[1] = ninetyDeg( projectPath( firstRoot, firstPoint) , projectPath( secondRoot, secondPoint ), false );
	arrAbsolutePoint[2] = ninetyDeg( projectPath( secondRoot, secondPoint ),  projectPath( firstRoot, firstPoint), true )
	arrAbsolutePoint[3] = projectPath( firstRoot, firstPoint );

	var arrReturn = [];

	for(var i in arrAbsolutePoint){
		var coordParts = arrAbsolutePoint[i].split(',');
		if( i == 0 ){
			diffX 	= coordParts[0];
			diffY 	= coordParts[1];
		} else {
			var prevCoordParts = arrAbsolutePoint[i-1].split(',');
			diffX = prevCoordParts[0] - coordParts[0];
			diffY = prevCoordParts[1] - coordParts[1];
		}
		arrReturn[i] = parseFloat(diffX).toFixed(3) + ',' + parseFloat(diffY).toFixed(3);
	}

	//console.log(arrAbsolutePoint);
	//console.log(arrReturn);

	return arrReturn;
	
});




/**
 Renders the path on the canvas  
 @skvConfig can contain 'class', 'id', 'd'
*/
function renderPath( skvPath ){

	var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');

	if( typeof skvPath['class'] !== 'undefined' ){
		path.setAttribute("class", skvPath['class']);
	}
	
	if( typeof skvPath['id'] !== 'undefined' ){
		path.setAttribute("id", skvPath['id'] );
	}

	if( typeof skvPath['d'] !== 'undefined' ){
		path.setAttribute("d", skvPath['d'] );
	}

	if( typeof skvPath['stroke-width'] !== 'undefined' ){
		path.setAttribute("stroke-width", skvPath['stroke-width'] );
	}

	$('svg').append(path); 
}




/* Returns boolean of whether or not all points in the */
/* polygon are within the boundaries of the map ------ */
var allPointsOnMap = (function(){
	//console.warn( "allPointsOnMap() is checking " + this.arrAbsolutePoints );
	for(var i in this.arrAbsolutePoints){
		if( !map.isPointOnYou( this.arrAbsolutePoints[i] ) ){
			return false;
		}
	}
	return true;
});



/* Path object 															*/
/* @id String A web standard id 										*/
/* @d String SVG standard path definition 								*/
var Path = (function(id, d){

	this.spawnOffsetSide = spawnOffsetSide;
	this.getCenter 		= getCenter;
	this.allPointsOnMap = allPointsOnMap;

	this.id = id;
	this.d = d;

	this.arrVerticesX = [];
	this.arrVerticesY = [];
	this.arrAbsolutePoints = [];

	var reg = /[0-9-.]*,[0-9-.]*/g;
	this.arrPoints = this.d.match(reg);

	cursorX = 0;
	cursorY = 0;
	for(var i in this.arrPoints){

		cursorX = cursorX + parseFloat( this.arrPoints[i].split(',')[0] );
		cursorY = cursorY + parseFloat( this.arrPoints[i].split(',')[1] );

		this.arrVerticesX[i] = cursorX;
		this.arrVerticesY[i] = cursorY;

		this.arrAbsolutePoints[i] = cursorX + ',' + cursorY;
	}

});





/* Populated in input box with mouse coordinates for debugging ------------------------ */
(function() {
    window.onmousemove = handleMouseMove;
    function handleMouseMove(event) {
        event = event || window.event; // IE-ism
        document.getElementById('mouseCoord').value = event.clientX + ',' + event.clientY;
    }
})();




/* Handles click event on mask which is the 	--------------- */
/* transparent layer that floats above the SVG 	--------------- */
$('#mask').click( function(){
	var mouseMode = $('input[name=mouseMode]:checked').val();
	var mouseCoord = document.getElementById('mouseCoord').value;

	var arrCoordParts = mouseCoord.split(',');
	var x = arrCoordParts[0];
	var y = arrCoordParts[1];

	if( mouseMode === 'isOccupied' ){
		isOccupied( x, y );
	} else if( mouseMode === 'redDot' ){
		debugDot( x, y, 'red' );
	} else if( mouseMode === 'nearestRoute' ){
		nearestRoute( x, y );
	} else if( mouseMode === 'placeProperty' ){
		placeProperty( x, y );
	} else if( mouseMode === 'deleteProperty' ){
		deleteProperty( x, y );
	}
	
});




/* Tests all paths on map to see if point is inside */
/* @coord co-ordinates of point ------------------- */ 
function isOccupied( x, y ){
	$.ajax({
        type: "GET",
        url: "/isOccupied/",
        data: { 'mapID': globals.mapID, 
        		'x': x, 
        		'y': y 
        	},
        dataType: "json"
    }).done(function(data) {
        console.log( data );
    });
}




/* Places a dot on the canvas for debugging purposes---------------------------- */
/* @cord The co-ordinates of where the dot should be placed -------------------- */
/* @colour *Optional* - Defaults to 'red' -------------------------------------- */
function debugDot( x, y, colour ){

	if(typeof colour === 'undefined'){
		colour = 'red';
	}

	var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
	circle.setAttribute("cx", x );
	circle.setAttribute("cy", y );
	circle.setAttribute("r", "2" );
	circle.setAttribute("fill", colour );
	circle.setAttribute("class", "Dot" );

	$('svg').append(circle); 
}




/* Places a path on the canvas for debugging purposes---------------------------- */
/* @arrPoints Array of points representing the path ----------------------------- */
/* @colour *Optional* - Defaults to 'red' --------------------------------------- */
function debugPath( arrPoints, colour ){

	if(typeof colour === 'undefined'){
		colour = 'red';
	}

	var d = 'M ';
	for( var i = 0, iLimit = arrPoints.length; i < iLimit; i++ ){
		d += ' ' + arrPoints[i].x + ',' + arrPoints[i].y;
	}

	var debugPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
	debugPath.setAttribute("d", d );
	debugPath.setAttribute("stroke", colour );
	debugPath.setAttribute("class", "DebugPath" );

	$('svg').append(debugPath); 
}




/* Send AJAX request to /nearestRoute/ and renders result */
function nearestRoute( x, y ){
	$.ajax({
        type: "GET",
        url: "/nearestRoute/",
        data: { 'mapID': globals.mapID, 
        		'x': x, 
        		'y': y 
        	},
        dataType: "json"
    }).done(function(data) {
		console.log( data );
		$('svg .Dot').remove();

		console.log( 'distanceToPointResult: ' + data.closestPointOnRoute.distanceToPointResult );
		debugPath( data.closestPointOnRoute.arrOppAndAdjSidesToA, 'orange' );
		debugPath( data.closestPointOnRoute.arrOppAndAdjSidesToC, 'green' );
		debugDot( data.closestPointOnRoute.arrPointA['x'], data.closestPointOnRoute.arrPointA['y'], 'red' );
		debugDot( data.closestPointOnRoute.arrPointB['x'], data.closestPointOnRoute.arrPointB['y'], 'blue' );
		debugDot( data.closestPointOnRoute.arrPointResult['x'], data.closestPointOnRoute.arrPointResult['y'], 'pink' );
    });
}




/* Send AJAX request to /placeProperty/ and renders result */ 
function placeProperty( x, y ){
	$.ajax({
        type: "GET",
        url: "/placeProperty/",
        data: { 'mapID': globals.mapID, 
        		'x': x, 
        		'y': y 
        	},
        dataType: "json"
    }).done(function(data) {
        if( data.success ){
        	renderPath( data['arrPath'] );
        }
    });
}




/* Tests all paths on map to see if point is inside */
/* @coord co-ordinates of point ------------------- */ 
function deleteProperty( x, y ){
	$.ajax({
        type: "GET",
        url: "/deleteProperty/",
        data: { 'mapID': globals.mapID, 
        		'x': x, 
        		'y': y 
        	},
        dataType: "json"
    }).done(function(data) {
        for( var i = 0; i <= data.length; i++ ){
        	$( 'svg .Property#property' + data[i] ).remove();
        }
    });
}




/* Handles click event to start spawning 	*/
$('#btnSpawnStart').click( spawn );




/* A function that creates buildings. Then calls itself.	*/
function spawn(){

	$('#spawnNotify').addClass('active');

	// Generate a pointer to a random existing path to build next to
	var pathPointer = Math.floor( Math.random() * ( arrPaths.length - 1 ) );
	console.log("Path Pointer: " + pathPointer);

	// Generate a pointer to a random side of the building to build adjascent to
	var sidePointer = Math.floor( Math.random() * 4 );
	console.log("Side Pointer: " + sidePointer);

	var arrPoints = arrPaths[pathPointer].spawnOffsetSide(sidePointer);
	var d = 'M ' + arrPoints.join(' ') + ' z';

	var id = "testPath";
	var testPath = new Path( id, d);
	testPath.varyMiddleTwo();


	var valid = true;
	for( var i in arrPaths ){
		if( polyCollision( testPath, arrPaths[i] ) ){
			console.warn("Invalid: Collision");
			valid = false;
		}
	}

	if( !testPath.allPointsOnMap() ){
		console.warn("Invalid: Some points were not on map");
		valid = false;
	}

	window.setTimeout( spawn, 10);
}



/* Handles click event to stop spawning 	*/
$('#btnSpawnStop').click( function(){
	var id = window.setTimeout(function() {}, 0);
	while (id--) {
	    window.clearTimeout(id);
	}
	$('#spawnNotify').removeClass('active');
});




$('#btnInitXRoads').click( function(){
	
	$.ajax({
        type: "GET",
        url: "/initCrossRoads/",
        data: { "mapID": globals.mapID },
        dataType: "json"
    }).done(function(data) {

        if( data.success ){
        	var i = iLimit = 0;
	        for( i = 0, iLimit = data.arrPaths.length; i < iLimit; i++ ){
	        	renderPath( data.arrPaths[i] );
	        }
        }

    });

});




$('#btnDrawRoutes').click( function(){
	$.ajax({
        type: "GET",
        url: "/GET/routes/",
        data: { "mapID": globals.mapID },
        dataType: "json"
    }).done(function(data) {

        var i = iLimit = 0;
        for( i = 0, iLimit = data.length; i < iLimit; i++ ){
        	renderPath( data[i] );
        }

    });
});




$('#btnDeleteRoutes').click( function(){
	$('svg .Route').remove();
});




$('#btnDrawProperties').click( function(){
	$.ajax({
        type: "GET",
        url: "/GET/properties/",
        data: { "mapID": globals.mapID },
        dataType: "json"
    }).done(function(data) {

        var i = iLimit = 0;
        for( i = 0, iLimit = data.length; i < iLimit; i++ ){
        	renderPath( data[i] );
        }

    });
});




/* Removes all paths from the SVG that represent a Property on the map */
$('#btnDeleteProperties').click( function(){
	$('svg .Property').remove();
});




$('#btnDrawFronts').click( function(){
	$.ajax({
        type: "GET",
        url: "/GET/propertyFronts/",
        data: { "mapID": globals.mapID },
        dataType: "json"
    }).done(function(data) {

        var i = iLimit = 0;
        for( i = 0, iLimit = data.length; i < iLimit; i++ ){
        	renderPath( data[i] );
        }

    });
});




$('#btnDeleteFronts').click( function(){
	$('svg .Front').remove();
});




