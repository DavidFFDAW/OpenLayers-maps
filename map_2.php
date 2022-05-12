<?php
    session_start();

    if(!isset($_SESSION['username'])) {
        header("Location: login.php");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $route = json_decode($_POST['route'], true);
        file_put_contents('map_2.json', json_encode($route, JSON_PRETTY_PRINT));
        // exit;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.7.0/css/ol.css" type="text/css" />
    <link rel="icon" type="image/png" href="./descarga.png"/>
    <script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.14.1/build/ol.js"></script>
    <title>Mapa</title>
    <style>
        html,body,#map { width:100%; height:100%; margin:0; }
        #map {
            position: absolute;
            z-index: 5;
        }
        #msg{
            position: absolute;
            z-index: 10;
            left: 50%;
            transform: translate(-50%, 5px);
            background-color: rgba(40,40,40,.8);
            padding: 10px;
            color: #eee;
            width: 350px;
            text-align: center;
        }
        .ol-control button{ 
        background-color: rgba(40, 40, 40, 0.85) !important;
        }
        .ol-control button:hover{ 
        background-color: rgba(40, 40, 40, 1) !important;
        }
        .col-picker {
            display: block;
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 12px;
            z-index: 10;
        }
        #stroke {
            width: 30px;
        }

        .pap {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            box-sizing: border-box;
            padding: 0 10px;
        }
        .botons{
            display: block;
            position: absolute;
            top: 50%;
            left: 5px;
            transform: translateY(-50%);
            z-index: 10;
        }
        .botons .b {
            display: block;
            padding: 10px 8px;
            border-radius: 20px;
            font-size: 16px;
            outline: none;
            border: none;
            cursor: pointer;
            margin: 15px 0;
            transition: all .2s;
        }
    </style>
</head>
<body>
    <div class="botons">
        <button class="b" onclick="saveRoute()">Guardar ruta</button>
        <button class="b" onclick="loadRoute()">Cargar ruta</button>
        <button class="b" onclick="deleteEntireRoute()">Borrar ruta</button>
        <button class="b" onclick="previousMove()">Atras</button>
    </div>
    <div class="col-picker">
        <input id="color" type="color" value="#5aad6c">
        <input id="stroke" type="number" value="4">
        <button onclick="changeColor(event)">Cambiar</button>
    </div>
    <div id="map"></div>
    <!-- <div id="msg">Click to add a point.</div> -->
    
    <!-- <script src="./map.js"></script> -->
    <script>
        
        var points = [],
        msg_el = document.getElementById('msg'),
        url_osrm_nearest = 'https://router.project-osrm.org/nearest/v1/driving/',
        url_osrm_route = 'https://router.project-osrm.org/route/v1/driving/',
        icon_url = './arrow-down-circle-fill.svg',
        vectorSource = new ol.source.Vector(),
        vectorLayer = new ol.layer.Vector({
            source: vectorSource
        }),
        styles = {
            route: new ol.style.Style({
                stroke: new ol.style.Stroke({
                    width: 6, color: [40, 40, 40, 0.8]
            })
        }),
        icon: new ol.style.Style({
            image: new ol.style.Icon({
                anchor: [0.5, 1],
            src: icon_url
            })
        })
    };

    console.clear();

    var initialCoords = [-494808.6826199734, 4400872.161600239];
    var previousCoords = [];
    var globalColor = '#5aad6c';
    var globalStrokeWidth = 4;
    var coordinates = [];


    const createLineStringBetweenTwoPoints = (pointsArray, stroke, color, marker = true) => {
        const finalColor = color || globalColor;
        const finalStroke = stroke || globalStrokeWidth;

        const featureLine = new ol.Feature({
            geometry: new ol.geom.LineString(pointsArray)
        });

        const vectorLine = new ol.source.Vector({});
        vectorLine.addFeature(featureLine);
        
        const vectorLineLayer = new ol.layer.Vector({
            source: vectorLine,
            style: new ol.style.Style({
                fill: new ol.style.Fill({ color: finalColor, weight: finalStroke }),
                stroke: new ol.style.Stroke({ color: finalColor, width: finalStroke })
            }),
            name: 'direction',
            type: 'DirectionLine'
        });

        return vectorLineLayer;
    }

    const createMarkerAt = (coords) => {
        coords[1] = coords[1] + 0.00001;
        const circle = new ol.Feature({
            geometry : new ol.geom.Point(ol.proj.fromLonLat(coords)),
            labelPoint: new ol.geom.Point(ol.proj.fromLonLat(coords)),
            name: 'My Point',
            size : 10
        });

        const vectorMarker = new ol.source.Vector({});
        vectorMarker.addFeature(circle);

        const vectorMarkerLayer = new ol.layer.Vector({
            source: vectorMarker,
            style: new ol.style.Style({
                image: new ol.style.Icon({
                    anchor: [0.2, 0.5],
                    src: icon_url
                })
            }),
            name: 'marker',
            type: 'Marker'
        });
        return vectorMarkerLayer;
    }


    function changeColor (ev) {
        const color = document.getElementById('color').value;
        console.log(color);
        const stroke = Number(document.getElementById('stroke').value);
        console.log(stroke);

        globalColor = color;
        globalStrokeWidth = stroke;
    }

    const layers = [
        new ol.layer.Tile({
            source: new ol.source.OSM()
        }),
        vectorLayer,
    ];
    const viewOptions = {
        center: initialCoords,
        zoom: 16,
        // minZoom: 14,
        maxZoom: 20,
        zoomAnimation: true,
        zoomFactor: 2,
        
    };
    const mapOptions = { target: 'map', layers: layers, view: new ol.View(viewOptions) };
    
    
    const map = new ol.Map(mapOptions);
    const zoomslider = new ol.control.ZoomSlider();
    map.addControl(zoomslider);


    // Añade linea en el mapa.
    map.on('click', async function(evt) {
        const coords = evt.coordinate;
        const lastCoords = previousCoords[previousCoords.length - 1] || coords;
        const points = [lastCoords, coords];       
        console.log(lastCoords);
        console.log(previousCoords);


        coordinates = [...coordinates, {
            line: points,
            color: globalColor,
            stroke: globalStrokeWidth,
            street: await getRoadName(coords),
        }];

        previousCoords = [...previousCoords, coords ];

        const marker = createMarkerAt(ol.proj.toLonLat(coords));
        const line = createLineStringBetweenTwoPoints(points, globalStrokeWidth, globalColor);
        
        map.addLayer(line);
        map.addLayer(marker);
    }); 

    async function getRoadName (coords) {
        const [lon,lat] = ol.proj.toLonLat(coords);
        const url = `http://nominatim.openstreetmap.org/reverse?format=json&lon=${lon}&lat=${lat}`;
        const response = await fetch(url);
        const data = await response.json();
        return { fullName: data.display_name || 'Probably water', road: data.address.road || 'Probably water' };
    }

    
    function loadRoute () {// window.addEventListener('load', function (evt) {
        const loadedRoute = JSON.parse(window.localStorage.getItem('route'));
        
        loadedRoute.forEach( 
            ({ line, stroke, color }) => {
                const finalLine = createLineStringBetweenTwoPoints(line, stroke, color);
                map.addLayer(finalLine);
            }
        );
    }
    // window.onload = loadRoute;

    function saveRoute () {
        // window.localStorage.setItem('route', JSON.stringify(coordinates));

        const form = document.createElement('form');
        form.setAttribute('method', 'POST');
        // form.setAttribute('action', '/');
        const inpt = document.createElement('input');
        inpt.setAttribute('type', 'hidden');
        inpt.setAttribute('name', 'route');
        inpt.setAttribute('value', JSON.stringify(coordinates));

        form.appendChild(inpt);
        document.body.appendChild(form);
        form.submit();
    }

    function previousMove () {
        coordinates = coordinates.slice(0,-1);
        previousCoords = previousCoords.slice(0, -1);
        const layersArray = map.getLayers().getArray();
        const reversed = [...layersArray].reverse();
        // will get the first direction of the array reversed which is the actual last direction
        const foundLayer = reversed.find( layer => layer.get('name') === 'direction');
        
        if (foundLayer) map.removeLayer(foundLayer);
    }

    function deleteEntireRoute () {
        coordinates = [];
        previousCoords = [];
        map.getLayers().getArray()
            .filter( layer => layer.get('name') === 'direction')
            .forEach( item => {
                map.removeLayer(item);
            }
        );
    }















    var utils = {
    getNearest: function(coord) {
        var coord4326 = utils.to4326(coord);    
        return new Promise(function(resolve, reject) {
            //make sure the coord is on street
            fetch(url_osrm_nearest + coord.join).then(function(response) { 
                // Convert to JSON
                return response.json();
                }).then(function(json) {
                    if (json.code === 'Ok') resolve(json.waypoints[0].location);
            else reject();
        });
    });
    },
    getCosaRara: function(coord){
        return new Promise(function(resolve, reject) {
            //make sure the coord is on street
            fetch(url_osrm_route + coord + '?overview=false').then(function(response) { 
                // Convert to JSON
                return response.json();
                }).then(function(json) {
                    if (json.code === 'Ok') resolve(json.waypoints[0].location);
            else reject();
        });
    });
    },
    createFeature: function(coord) {
        var feature = new ol.Feature({
            type: 'place',
        geometry: new ol.geom.Point(ol.proj.fromLonLat(coord))
        });
        feature.setStyle(styles.icon);
        vectorSource.addFeature(feature);
    },
    createRoute: function(polyline) {
        console.log('createRoute polyline', polyline);
        // route is ol.geom.LineString
        var route = new ol.format.Polyline({
            factor: 1e5
        }).readGeometry(polyline, {
            dataProjection: 'EPSG:4326',
            featureProjection: 'EPSG:3857'
        });
        var feature = new ol.Feature({
            type: 'route',
            geometry: route
        });
        feature.setStyle(styles.route);
        vectorSource.addFeature(feature);
    },
    to4326: function(coord) {
        return ol.proj.transform([
            parseFloat(coord[0]), parseFloat(coord[1])
            ], 'EPSG:3857', 'EPSG:4326');
        }
    };
    </script>

</body>
</html>