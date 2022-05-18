<?php
    // session_start();

    // if(!isset($_SESSION['token'])) {
    //     header("Location: login.php");
    // }

    // if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //     $route = json_decode($_POST['route'], true);
    //     file_put_contents('map_2.json', json_encode($route, JSON_PRETTY_PRINT));
    //     // exit;
    // }
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
    <div id="msg"></div>
    <!-- <div id="msg">Click to add a point.</div> -->
    
    <!-- <script src="./map.js"></script> -->
    <script>
        
        var points = [],
    msg_el = document.getElementById('msg'),
    url_osrm_nearest = '//router.project-osrm.org/nearest/v1/foot/',
    url_osrm_route = '//router.project-osrm.org/route/v1/osrm-extract/',
    icon_url = '//cdn.rawgit.com/openlayers/ol3/master/examples/data/icon.png',
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

var map = new ol.Map({
  target: 'map',
  layers: [
    new ol.layer.Tile({
      source: new ol.source.OSM()
    }),
    vectorLayer
  ],
  view: new ol.View({
    center: [-494808.6826199734, 4400872.161600239],
    zoom: 18
  })
});

map.on('click', function(evt){
  utils.getNearest(evt.coordinate).then(function(coord_street){
    var last_point = points[points.length - 1];
    var points_length = points.push(coord_street);

    utils.createFeature(coord_street);

    if (points_length < 2) {
      msg_el.innerHTML = 'Click to add another point';
      return;
    }

    //get the route
    var point1 = last_point.join();
    var point2 = coord_street.join();
    
    fetch(url_osrm_route + point1 + ';' + point2+'?exclude=motorway').then(function(r) { 
      return r.json();
    }).then(function(json) {
      if(json.code !== 'Ok') {
        msg_el.innerHTML = 'No route found.';
        return;
      }
      msg_el.innerHTML = 'Route added';
      //points.length = 0;
      utils.createRoute(json.routes[0].geometry);
    });
  });
});

var utils = {
  getNearest: function(coord){
    var coord4326 = utils.to4326(coord);    
    return new Promise(function(resolve, reject) {
      //make sure the coord is on street
    //   fetch(url_osrm_nearest + coord4326.join()).then(function(response) { 
    //     // Convert to JSON
    //     return response.json();
    //   }).then(function(json) {
    //     if (json.code === 'Ok') resolve(json.waypoints[0].location);
    //     else reject();
    //   });
    resolve(coord4326);
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