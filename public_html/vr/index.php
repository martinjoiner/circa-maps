<!DOCTYPE html>
<html>
<head>

    <title>VR - Circa Maps</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

    <style>
        body {
            margin: 0;
            padding: 0;
        }
        canvas {
            display: block;
        }
    </style>

</head>
<body>

    <?php

    if( !array_key_exists('mapID', $_GET) ){
        $_GET['mapID'] = 1;
    }

    require $_SERVER['DOCUMENT_ROOT'] . '/../vendor/autoload.php';

    $map = new \App\MapComplete($_GET['mapID']);

    ?>

    <div id=container></div>

    <script src="js/three.min.js"></script>
    <script src="js/tween.min.js"></script>
    <script src="js/WebVR.js"></script>
    <script src="js/OrbitControls.js"></script>

    <script src="http://code.jquery.com/jquery-2.2.4.min.js"
        integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
        crossorigin="anonymous"></script>

    <script>

        map = {
            id: <?=$map->getId()?>,
            name: '<?=$map->getName()?>',
            width: <?=$map->getWidth()?>,
            height: <?=$map->getHeight()?>
        };

        WEBVR.checkAvailability().catch( function( message ) {

            document.body.appendChild( WEBVR.getMessageContainer( message ) );

        } );

        //

        var clock = new THREE.Clock();

        var container;
        var camera, scene, raycaster, renderer;
        var controls; // Only set when developing in browser
        var propertyGroup;

        var isMouseDown = false;

        var INTERSECTED;
        var crosshair;

        var cows = [];

        init();
        animate();

        function circleShape(circleRadius, clockwise) {
            var circle = new THREE.Shape();
            circle.moveTo( circleRadius, 0 );
            // aX, aY, aRadius, aStartAngle, aEndAngle, aClockwise
            circle.absarc( 0, 0, circleRadius, 0, Math.PI * 2, clockwise );
            return circle;
        }

        function ringShape( innerRadius, outerRadius ) {
            var arcShape = circleShape(outerRadius, true);
            
            var holePath = circleShape(innerRadius, false);

            arcShape.holes.push( holePath );
            return arcShape;
        }

        function coloredRingMesh(color, radius, ringWidth) {
            var options = { 
                amount: 0.1,              // default 100, only used when path is null
                bevelEnabled: false, 
                bevelSegments: 2, 
                steps: 1,                // default 1, try 3 if path defined
                extrudePath: null        // or path
            };

            var ring_geometry = new THREE.ExtrudeGeometry( ringShape( radius, radius+ringWidth), options );
            var ring_material = new THREE.MeshLambertMaterial( {
                color: color,
                opacity: 0.5,
                transparent: true
            } );

            return new THREE.Mesh( ring_geometry, ring_material );
        }

        function init() {

            container = document.createElement( 'div' );
            document.body.appendChild( container );

            scene = new THREE.Scene();
            scene.background = new THREE.Color( 0x505050 );

            camera = new THREE.PerspectiveCamera( 70, window.innerWidth / window.innerHeight, 0.1, Math.max(map.width, map.height) );
            scene.add( camera );

            crosshair = new THREE.Mesh(
                new THREE.RingGeometry( 0.02, 0.04, 32 ),
                new THREE.MeshBasicMaterial( {
                    color: 0xffffff,
                    opacity: 0.5,
                    transparent: true
                } )
            );
            crosshair.position.z = - 2;
            camera.add( crosshair );
            camera.position.x = 0;
            camera.position.y = 0.5;
            camera.position.z = 2;

            //

            scene.add( new THREE.HemisphereLight( 0x606060, 0x404040 ) );

            var light = new THREE.DirectionalLight( 0xffffff );
            light.position.set( 1, 1, 1 ).normalize();
            scene.add( light );


            // Desert colour FLOOR

            var plane_geometry = new THREE.PlaneGeometry(map.width, map.height);
            plane_geometry.rotateX( Math.PI / -2 );
            var plane_material = new THREE.MeshBasicMaterial( { color: 0xecd888 } ); 
            var plane = new THREE.Mesh( plane_geometry, plane_material );
            plane.name = "Floor";
            scene.add(plane);


            // Raycaster for detecting mouse-over

            raycaster = new THREE.Raycaster();

            renderer = new THREE.WebGLRenderer( { antialias: true } );
            renderer.setPixelRatio( window.devicePixelRatio );
            renderer.setSize( window.innerWidth, window.innerHeight );
            container.appendChild( renderer.domElement );

            renderer.vr.enabled = true;

            WEBVR.getVRDisplay( function ( display ) {

                renderer.vr.setDevice( display );

                document.body.appendChild( WEBVR.getButton( display, renderer.domElement ) );
                if( !display ){
                    console.log('No displays, initialising OrbitControls');

                    controls = new THREE.OrbitControls( camera, renderer.domElement );
                    controls.addEventListener( 'change', render );
                    controls.target.set(0, 10, 0);

                    camera.position.x = 140;
                    camera.position.y =  200;
                    camera.position.z =  250;

                    controls.update();

                }

            } );

            renderer.domElement.addEventListener( 'mousedown', onMouseDown, false );
            renderer.domElement.addEventListener( 'mouseup', onMouseUp, false );
            renderer.domElement.addEventListener( 'touchstart', onMouseDown, false );
            renderer.domElement.addEventListener( 'touchend', onMouseUp, false );

            //

            window.addEventListener( 'resize', onWindowResize, false );

            $.ajax({
                type: "GET",
                url: "/api/properties/",
                data: { 
                    mapID: map.id,
                    format: 'points'
                },
                dataType: "json"
            }).done(function(properties) {

                var extrude_options = { 
                    amount: 10, 
                    bevelEnabled: false, 
                    bevelSegments: 2, 
                    steps: 1,                // default 1, try 3 if path defined
                    extrudePath: null        // or path
                };

                var property_material_config = {
                    color: 0x5f3730,
                    opacity: 0.8,
                    transparent: true
                };

                var property,
                    property_shape,
                    property_material,
                    property_geometry;

                propertyGroup = new THREE.Group()

                for( var i = 0, iLimit = properties.length; i < iLimit; i++ ){
                    property = properties[i];

                    property_shape = new THREE.Shape();

                    property_material = new THREE.MeshLambertMaterial( property_material_config );

                    property_shape.moveTo( property.points[0].x, property.points[0].y );
                    for( var p=0, pLimit = property.points.length; p < pLimit; p++ ){
                        property_shape.lineTo( property.points[p].x, property.points[p].y );
                    }
                    property_geometry = new THREE.ExtrudeGeometry( property_shape, extrude_options );
                    property_mesh = new THREE.Mesh( property_geometry, property_material );
                    property_mesh.name = property.id;
                    propertyGroup.add( property_mesh );
                }

                propertyGroup.rotateX( Math.PI / -2);
                propertyGroup.translateX(- map.width / 2);
                propertyGroup.translateY(- map.height / 2);
                scene.add( propertyGroup );

            });

        }


        function onMouseDown() {

            isMouseDown = true;

        }

        function onMouseUp() {

            isMouseDown = false;

        }

        function onWindowResize() {

            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();

            renderer.setSize( window.innerWidth, window.innerHeight );

        }

        //

        function animate() {

            renderer.animate( render );
            if( controls ){
                controls.update();
            }

        }

        function render() {

            TWEEN.update();

            var delta = clock.getDelta() * 60;

            if ( isMouseDown === true ) {

            }

            // find intersections

            raycaster.setFromCamera( { x: 0, y: 0 }, camera );

            var intersects = raycaster.intersectObjects( propertyGroup.children );

            if ( intersects.length > 0 ) {

                if ( INTERSECTED != intersects[ 0 ].object ) {

                    if ( INTERSECTED ) INTERSECTED.material.emissive.setHex( INTERSECTED.currentHex );

                    INTERSECTED = intersects[ 0 ].object;
                    INTERSECTED.currentHex = INTERSECTED.material.emissive.getHex();
                    INTERSECTED.material.emissive.setHex( 0xbf3600 );

                }

            } else {

                if ( INTERSECTED ) INTERSECTED.material.emissive.setHex( INTERSECTED.currentHex );

                INTERSECTED = undefined;

            }

            renderer.render( scene, camera );

        }

    </script>

</body>
</html>
