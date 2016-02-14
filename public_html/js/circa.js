
var globals = {};
globals.mapID = $('input#mapID').val();




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
		$('svg #'+ skvPath['id']).remove();
	}

	if( typeof skvPath['d'] !== 'undefined' ){
		path.setAttribute("d", skvPath['d'] );
	}

	if( typeof skvPath['stroke-width'] !== 'undefined' ){
		path.setAttribute("stroke-width", skvPath['stroke-width'] );
	}

	$('svg').append(path); 
}




/* Populate input box with mouse coordinates for debugging ------------------------ */
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
	} else if( mouseMode === 'offsetSides' ){
		offsetSides( x, y );
	} else if( mouseMode === 'improvePropertyAtPoint' ){
		improvePropertyAtPoint( x, y );
	}
	
});




/* Tests all paths on map to see if point is inside ------------------------------- */
/* @x, @y co-ordinates of point --------------------------------------------------- */ 
function isOccupied( x, y ){
	$.ajax({
        type: "GET",
        url: "/GET/isOccupied/",
        data: { 'mapID': globals.mapID, 
        		'x': x, 
        		'y': y 
        	},
        dataType: "json"
    }).done(function(data) {
        console.log( data );
        if( data.isOccupied && data.occupationType === 'PROPERTY' ){
            debugPath( data.propertyInfo.arrAreaData.rightAngledTriangles[0], 'red' );
    		debugPath( data.propertyInfo.arrAreaData.rightAngledTriangles[1], 'orange' );
    		debugPath( data.propertyInfo.arrAreaData.rightAngledTriangles[2], 'yellow' );
    		debugPath( data.propertyInfo.arrAreaData.rightAngledTriangles[3], 'green' );
        }
    });
}




/* Places a dot on the canvas for debugging purposes---------------------------- */
/* @x, @y The co-ordinates of where the dot should be placed ------------------- */
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
	debugPath.setAttribute("fill", colour );
	debugPath.setAttribute("class", "DebugPath" );

	$('svg').append(debugPath); 
}




/* Send AJAX request to /nearestRoute/ and renders result */
function nearestRoute( x, y ){
	$.ajax({
        type: "GET",
        url: "/GET/nearestRoute/",
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
/* @x, @y - co-ordinates of point ------------------------ */ 
function placeProperty( x, y ){
	$.ajax({
        type: "GET",
        url: "/POST/placeProperty/",
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




/* Tests all paths on map to see if point is inside ------ */
/* @x, @y - co-ordinates of point ------------------------ */ 
function deleteProperty( x, y ){
	$.ajax({
        type: "GET",
        url: "/DELETE/property/",
        data: { 'mapID': globals.mapID, 
        		'x': x, 
        		'y': y 
        	},
        dataType: "json"
    }).done(function(data) {
        for( var i = 0; i < data.length; i++ ){
        	$( 'svg .Property#property' + data[i] ).remove();
        }
    });
}




/* Queries a point on the map. If occupied by a property -- */
/* it returns the offset points of that property ---------- */
/* @x, @y - co-ordinates of point ------------------------- */ 
function offsetSides( x, y ){
	$.ajax({
        type: "GET",
        url: "/GET/offsetSides/",
        data: { 'mapID': globals.mapID, 
        		'x': x, 
        		'y': y 
        	},
        dataType: "json"
    }).done(function(data) {
    	renderSides( data );
    });
}




/* Queries a point on the map. If occupied by a property ------------------ */
/* it returns the offset points of that property's neighbouring properties  */
/* @x, @y - co-ordinates of point ----------------------------------------- */ 
function improvePropertyAtPoint( x, y ){
	$.ajax({
        type: "GET",
        url: "/PUT/improvePropertyAtPoint/",
        data: { 'mapID': globals.mapID, 
        		'x': x, 
        		'y': y 
        	},
        dataType: "json"
    }).done(function(data) {
    	//renderSides( data.arrNeighboursOffsetSides );
        console.log( data.cntSidesReplaced + ' sides replaced' );
        if( data.cntSidesReplaced ){
        	renderPath( data.path );
        }
    });
}




function renderSides( arrSides ){

    var arrCols = [ 'purple', 'green', 'blue', 'red' ];
    for( var i = 0; i < arrSides.length; i++ ){
    	debugDot( arrSides[i][0]['x'], arrSides[i][0]['y'], arrCols[i] );
    	debugDot( arrSides[i][1]['x'], arrSides[i][1]['y'], arrCols[i] );
    }
}




/* Handles click event to start spawning 	*/
$('#btnSpawnStart').click( spawn );




/* A function that creates buildings. Then calls itself.	*/
function spawn(){

	$('#spawnNotify').addClass('active');

	// Generate a pointer to a random existing path (property) to build next to

	// Generate a pointer to a random side of the property to build adjascent to

	// Call the spanOffsetSide() method to get a new set of points

	// Create a new object of class Path with generated points

	// Vary the 2 points away from the side slightly to give organic shape

	// Test the new property does not collide with any existing properties

	// Make it a permenant change if it's all good

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
        url: "/POST/initCrossRoads/",
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




