

/** Map class. The big fat main badger. */
Map = function(){

    /** jQuery element object */
    this.svg = $('svg');

    /** jQuery element object */
    this.mask = $('#mask');

    /** {integer} database ID of the map */
    this.id = parseInt( $('input#mapID').val() );

    /** {string} Can be 'isOccupied'... */
    this.mode = '';

}




/**
 * Method on Map Class: Renders the path on the canvas  
 *
 * @param skvPath Can contain 'class', 'id', 'd'
 */
Map.prototype.renderPath = function( skvPath ){

	var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');

	if( typeof skvPath['class'] !== 'undefined' ){
		path.setAttribute("class", skvPath['class']);
	}
	
	if( typeof skvPath['id'] !== 'undefined' ){
		path.setAttribute("id", skvPath['id'] );

        // Purge any elements with the same ID
		$('svg #'+ skvPath['id']).remove();
	}

	if( typeof skvPath['d'] !== 'undefined' ){
		path.setAttribute("d", skvPath['d'] );
	}

	if( typeof skvPath['stroke-width'] !== 'undefined' ){
		path.setAttribute("stroke-width", skvPath['stroke-width'] );
	}

	this.svg.append(path); 
};




/**
 * Method on Map class: Places a dot on the canvas for debugging purposes
 *
 * @param x The co-ordinates of where the dot should be placed 
 * @param y 
 * @param {string} colour *Optional* - Defaults to 'red' 
 * @param {string} dotClass *Optional* - Defaults to 'red' 
 */
Map.prototype.debugDot = function( x, y, colour, dotClass ){

    if(typeof colour === 'undefined'){
        colour = 'red';
    }

    if(typeof dotClass === 'undefined'){
        dotClass = 'Dot';
    }

    var circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
    circle.setAttribute("cx", x );
    circle.setAttribute("cy", y );
    circle.setAttribute("r", "2" );
    circle.setAttribute("fill", colour );
    circle.setAttribute("class", dotClass );

    this.svg.append(circle); 
};




/**
 * Method on Map class: Places a path on the canvas for debugging purposes
 * 
 * @param {array} arrPoints of points representing the path 
 * @param {string} colour *Optional* - Defaults to 'red' 
 */
Map.prototype.debugPath = function( arrPoints, colour ){

    if(typeof colour === 'undefined'){
        colour = 'red';
    }

    var d = 'M ';
    for( var i = 0, iLimit = arrPoints.length; i < iLimit; i++ ){
        d += ' ' + arrPoints[i].x + ',' + arrPoints[i].y;
    }

    var path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute("d", d );
    path.setAttribute("stroke", colour );
    path.setAttribute("fill", colour );
    path.setAttribute("class", "DebugPath" );

    this.svg.append(path); 
}




/**
 * Method on Map class: Sets the mod
 *
 * @param {string} mode
 */
Map.prototype.setMode = function( mode ){
    this.mode = mode;
    window.localStorage.mapMode = mode;
    if( mode === 'isOccupied' ){
        this.mask.css( 'cursor', 'help');
    } else {
        this.mask.css( 'cursor', 'default');
    }
}




/** Define a global instance of Map class */
map = new Map();




/** If any of the radio button values change, set the mode to reflect */
$('.mouseMode input').change( function(){
    map.setMode( $(this).val() );
});




mouseCoordInput = document.getElementById('mouseCoord');

/** Populate input box with mouse coordinates for debugging */
document.getElementById('mask').addEventListener('mousemove', function(e){
    mouseCoordInput.value = ( e.pageX - this.offsetLeft ) + ',' + ( e.pageY - this.offsetTop );
});




/** Handles click event on mask (the transparent layer that floats in front of the SVG) */
$('#mask').click( function(){

	var arrCoordParts = document.getElementById('mouseCoord').value.split(','),
	   x = arrCoordParts[0],
	   y = arrCoordParts[1];

	if( map.mode === 'isOccupied' ){
		isOccupied( x, y );
	} else if( map.mode === 'redDot' ){
		map.debugDot( x, y, 'red' );
	} else if( map.mode === 'nearestRoute' ){
		nearestRoute( x, y );
	} else if( map.mode === 'placeProperty' ){
		placeProperty( x, y );
	} else if( map.mode === 'deleteProperty' ){
		deleteProperty( x, y );
	} else if( map.mode === 'offsetSides' ){
		offsetSides( x, y );
	} else if( map.mode === 'improvePropertyAtPoint' ){
		improvePropertyAtPoint( x, y );
	}
	
});




/**
 * Tests all paths on map to see if point is inside 
 *
 * @param x Co-ordinates of point
 * @param y 
 */ 
function isOccupied( x, y ){
	$.ajax({
        type: "GET",
        url: "/GET/isOccupied/",
        data: { 'mapID': map.id, 
        		'x': x, 
        		'y': y 
        	},
        dataType: "json"
    }).done(function(data) {
        console.log( data );
        if( data.isOccupied && data.occupationType === 'PROPERTY' ){
            map.debugPath( data.propertyInfo.arrAreaData.rightAngledTriangles[0], 'red' );
    		map.debugPath( data.propertyInfo.arrAreaData.rightAngledTriangles[1], 'orange' );
    		map.debugPath( data.propertyInfo.arrAreaData.rightAngledTriangles[2], 'yellow' );
    		map.debugPath( data.propertyInfo.arrAreaData.rightAngledTriangles[3], 'green' );
        }
    });
}




/**
 * Send AJAX request to /GET/nearestRoute/ and renders result 
 *
 * @param x
 * @param y
 */
function nearestRoute( x, y ){
	$.ajax({
        type: "GET",
        url: "/GET/nearestRoute/",
        data: { 'mapID': map.id, 
        		'x': x, 
        		'y': y 
        	},
        dataType: "json"
    }).done(function(data) {
		console.log( data );
		$('svg .Dot').remove();

		map.debugPath( data.closestPointOnRoute.arrOppAndAdjSidesToA, 'orange' );
		map.debugPath( data.closestPointOnRoute.arrOppAndAdjSidesToC, 'green' );
		map.debugDot( data.closestPointOnRoute.arrPointA['x'], data.closestPointOnRoute.arrPointA['y'], 'red' );
		map.debugDot( data.closestPointOnRoute.arrPointB['x'], data.closestPointOnRoute.arrPointB['y'], 'blue' );
		map.debugDot( data.closestPointOnRoute.arrPointResult['x'], data.closestPointOnRoute.arrPointResult['y'], 'pink' );
    });
}




/* Send AJAX request to /placeProperty/ and renders result */ 
/* @x, @y - co-ordinates of point ------------------------ */ 
function placeProperty( x, y ){
	$.ajax({
        type: "GET",
        url: "/POST/placeProperty/",
        data: { 'mapID': map.id, 
        		'x': x, 
        		'y': y 
        	},
        dataType: "json"
    }).done(function(data) {
        if( data.success ){
        	map.renderPath( data['arrPath'] );
        }
    });
}




/* Tests all paths on map to see if point is inside ------ */
/* @x, @y - co-ordinates of point ------------------------ */ 
function deleteProperty( x, y ){
	$.ajax({
        type: "GET",
        url: "/DELETE/property/",
        data: { 'mapID': map.id, 
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
        data: { 'mapID': map.id, 
        		'x': x, 
        		'y': y 
        	},
        dataType: "json"
    }).done(function(data) {
        console.log(data);
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
        data: { 'mapID': map.id, 
        		'x': x, 
        		'y': y 
        	},
        dataType: "json"
    }).done(function(data) {
    	//renderSides( data.arrNeighboursOffsetSides );
        console.log( data );
        if( data.cntSidesReplaced ){
        	map.renderPath( data.path );
        }
    });
}




function renderSides( arrSides ){

    var arrCols = [ 'purple', 'green', 'blue', 'red' ];
    for( var i = 0; i < arrSides.length; i++ ){
    	map.debugDot( arrSides[i][0]['x'], arrSides[i][0]['y'], arrCols[i] );
    	map.debugDot( arrSides[i][1]['x'], arrSides[i][1]['y'], arrCols[i] );
    }
}




/** Handles click event to start spawning */
$('#btnSpawnStartStop').click( function(e){

    e.preventDefault();

    if( $(this).hasClass('active') ){
        $(this).removeClass('active').find('span').html('Start Spawning');
        stopSpawning();
    } else {
        $(this).addClass('active').find('span').html('Stop Spawning');
        startSpawning()
    }

});




/** A function that creates buildings. Then calls itself. */
function startSpawning(){

	//window.setTimeout( spawn, 10);
}




/** Handles click event to stop spawning */
function stopSpawning(){
	// var id = window.setTimeout(function() {}, 0);
	// while (id--) {
	//     window.clearTimeout(id);
	// }
}




/** AJAX call to server to /POST/initCrossRoads/ */
$('#btnInitXRoads').click( function(){
    
    $.ajax({
        type: "GET",
        url: "/POST/initCrossRoads/",
        data: { "mapID": map.id },
        dataType: "json"
    }).done(function(data) {

        if( data.success ){
            for( var i = 0, iLimit = data.arrPaths.length; i < iLimit; i++ ){
                map.renderPath( data.arrPaths[i] );
            }
        }

    });

});




/** AJAX call to server to /GET/mostIsolatedPoint/ */
$('#btnMostIsolated').click( function(){
	
	$.ajax({
        type: "GET",
        url: "/GET/mostIsolatedPoint/",
        data: { "mapID": map.id },
        dataType: "json"
    }).done(function(data) {

        console.log( data );
        map.debugDot( data.point.x, data.point.y );

    });

});




/** Render paths on the SVG map to represent routes */
$('#btnDrawRoutes').click( function(){
	$.ajax({
        type: "GET",
        url: "/GET/routes/",
        data: { "mapID": map.id },
        dataType: "json"
    }).done(function(data) {

        for( var i = 0, iLimit = data.length; i < iLimit; i++ ){
        	map.renderPath( data[i] );
        }

    });
});




/** Delete paths on the SVG that represent routes */
$('#btnDeleteRoutes').click( function(){
	$('svg .Route').remove();
});




/** Render paths on the SVG map to represent properties */
$('#btnDrawProperties').click( function(){
	$.ajax({
        type: "GET",
        url: "/GET/properties/",
        data: { "mapID": map.id },
        dataType: "json"
    }).done(function(data) {

        for( var i = 0, iLimit = data.length; i < iLimit; i++ ){
        	map.renderPath( data[i] );
        }

    });
});




/** Removes all paths from the SVG that represent a Property on the map */
$('#btnDeleteProperties').click( function(){
	$('svg .Property').remove();
});




/** Render red lines to indicate the fronts of each property */
$('#btnDrawFronts').click( function(){
	$.ajax({
        type: "GET",
        url: "/GET/propertyFronts/",
        data: { "mapID": map.id },
        dataType: "json"
    }).done(function(data) {

        for( var i = 0, iLimit = data.length; i < iLimit; i++ ){
        	map.renderPath( data[i] );
        }

    });
});




/** Delete the red lines that indicate the fronts properties */
$('#btnDeleteFronts').click( function(){
	$('svg .Front').remove();
});




/** Render yellow dots to indicate all the junctions on the map */
$('#btnDrawJunctions').click( function(){
    $.ajax({
        type: "GET",
        url: "/GET/junctions/",
        data: { "mapID": map.id },
        dataType: "json"
    }).done(function(data) {

        console.log(data);
        for( var i = 0, iLimit = data.length; i < iLimit; i++ ){
            
            map.debugPath( data[i].segmentA, 'red' );
            map.debugPath( data[i].segmentB, 'blue' );
            map.debugDot( data[i].point.x, data[i].point.y, 'yellow', 'junction' );
            
        }

    });
});




/** Delete the yellow dots that indicate the junctions */
$('#btnDeleteJunctions').click( function(){
    $('svg .junction').remove();
});




/**
 * On load, check local storage to see if a map mode has been saved
 */
if( typeof window.localStorage.mapMode !== 'undefined' ){
    $('input[value=' + window.localStorage.mapMode + ']').prop('checked', true).change();
} else {
    $('input[value=isOccupied]').prop('checked', true).change();
}



/**
 * Just-for-fun self-executing function to log a colorful welcome message in the console
 */
(function(){

    var style = 'color: #f78800; background: #333; padding: 6px; ';

    console.log("%c%s",
            style + 'font-size: 18px;',
            ' Circa Maps  ');

    console.log("%c%s",
            style + 'font-size: 13px;',
            '        by        ');

    console.log("%c%s",
            style + 'font-size: 18px;',
            'Martin Joiner');

})();

