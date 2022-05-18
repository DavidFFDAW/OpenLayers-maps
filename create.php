<?php include dirname(__FILE__).DIRECTORY_SEPARATOR.'includes/headers.php'; ?>

<style>
.w{width: 100% !important}
.corner.labeled.input.w{ padding: 20px 15px; }
#stroke {width: 30px;}
#map {position: absolute; z-index: 5; width: 100%;top: 0;left: 0;border-radius: 50px;height: 50vw;}
</style>

<script src="https://cdn.jsdelivr.net/gh/openlayers/openlayers.github.io@master/en/v6.14.1/build/ol.js"></script>

<div style="box-sizing: border-box; padding: 5%;">
    <h3 class="ui center aligned header">Creacion de evento</h3>
    <div class="ui two column doubling grid">
        <div class="column">
            <h3>Options</h3>
            <div class="ui styled fluid accordion">
                <div class="title" onclick="openAccordionChangeColor(event,'data')">
                    <i class="dropdown icon"></i>
                    <i class="circular orange info icon"></i>            
                    Datos
                </div>

                <div class="content" data-step="data">
                    <div class="ui corner labeled input w">
                        <label for="" class="ui label orange">Nombre</label>
                        <input type="text" placeholder="Nombre">
                    </div>
                    <div class="ui corner labeled input w">
                        <label for="" class="ui label orange">Fecha de evento</label>
                        <input type="date" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="ui corner labeled input w">
                        <label for="" class="ui label orange">Localización</label>
                        <input type="text" placeholder="Localización">
                    </div>
                </div>
            </div>
            <div class="ui styled fluid accordion" >
                <div class="title" onclick="openAccordionChangeColor(event,'route')">
                    <i class="dropdown icon"></i>
                    <i class="teal circular truck icon"></i>            
                    Ruta
                </div>

                <div class="content" data-step="route">
                    <button class="ui primary button" type="submit">
                        Enviar
                    </button>
                </div>
            </div>
            <div class="ui styled fluid accordion" >
                <div class="title" onclick="openAccordionChangeColor(event,'map')">
                    <i class="dropdown icon"></i>
                    <i class="teal circular map icon"></i>            
                    Opciones de Mapa
                </div>

                <div class="content" data-step="map">
                    <button class="ui primary button" type="submit">
                        Enviar
                    </button>
                </div>
            </div>
        </div>
        <div class="column" style="position: sticky; top: 50px; left: 50px;">
            <h3>Map</h3>
            <div class="ui segment" style="position: relative; width: 100%; min-height: 250px;">
                <div style="width: 100%; min-height: 250px;" id="map"></div>
            </div>
        </div>
    </div>
</div>

<script>
    function openAccordionChangeColor(ev, step) {
        const allIcons = [...document.querySelectorAll('div.ui.styled.fluid.accordion .title .circular')];
        const allContents = [...document.querySelectorAll('div.ui.styled.fluid.accordion .content.active')];

        allIcons.forEach(icon => {
            icon.classList.remove('orange');
            icon.classList.add('teal');
        });
        
        allContents.filter(it => it.dataset.step !== step).forEach(content => {
            content.classList.remove('active');
        });

        const current = ev.target.children[1];
        const currentContent = ev.target.nextElementSibling;
        // console.log(currentContent);
        current.classList.remove('teal');
        current.classList.add('orange');
    }


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
        // moveCoords(coords);
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

    // function moveCoords (coords) {
    //     console.log(coords);
    //     console.log(map.getProjection());
    //     var olCoordinates = ol.proj.transform(coords, 'EPSG:4326', 'EPSG:3857')
    //     console.log(olCoordinates);
    //     map.getView().setCenter(olCoordinates);
    //     // map.getView().setZoom(10);
    // }
</script>

<?php include dirname(__FILE__).DIRECTORY_SEPARATOR.'includes/footer.php'; ?>